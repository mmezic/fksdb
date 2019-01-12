<?php


namespace FKSDB;


use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class Router {

    const path = '/';
    const domain = [
        'cz' => 'fykos.cz',
        'org' => 'fykos.org',

        # Default top level domain
        'tld' => 'cz',

        # Main domain
        'host' => 'fykos',
    ];
    const subdomain = [
        'auth' => 'auth',

        # Main domain for the application.
        'db' => 'db',
    ];


    # Pipe-separated list of presenters w/out module
    const rootPresenters = 'settings|github|my-payment';

    # Pipe-separated list of modules
    const modules = ['org', 'public'];

    # Pipe-separated list of contest names
    const contests = ['fykos', 'vyfuk'];


    public static function createRouter(): RouteList {
        $routeList = new RouteList();
        $routeList[] = new Route(
            'index.php', [
            'module' => 'Public',
            'presenter' => 'Dashboard',
            'action' => 'default'],
            [Route::ONE_WAY, Route::SECURED]);

        $routeList[] = new Route(
            'web-service/<action>',
            [
                'module' => 'Org',
                'presenter' => 'WebService',
                'action' => 'default',
            ], [Route::ONE_WAY, Route::SECURED]);

        # Cool URL
        $routeList[] = new Route('%path%<contestId %contests%><year [0-9]+>[.<series [0-9]+>]/q/<qid>',
            [
                'subdomain' => '%subdomain.db%',
                'tld' => '%domain.tld%',
                'module' => 'Org',
                'presenter' => 'Export',
                'action' => 'execute',
                'contestId' => ['filterTable' => '%inverseContestMapping%']]
            [Route::SECURED]);

        # Central authentication domain (+ logout must be enabled at each domain too)
        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%<action logout>',
            [
                'presenter' => 'Authentication',
                'subdomain' => '%subdomain.db%',
                'tld' => '%domain.tld%',
            ],
            [Route::SECURED]);
        $routeList[] = new Route(
            '//%subdomain.auth%.%domain.cz%%path%<action login|logout|fb-login|recover>',
            [
                'presenter' => 'Authentication',
                'subdomain' => '%subdomain.auth%',
                'tld' => '%domain.tld%',],
            [Route::SECURED]);

        # Registration must be at the same domain as central authentication'
        $routeList[] = new Route('//[!<subdomain>].%domain.cz%%path%<presenter register>/[<contestId %contests%>/[year<year [0-9]+>/[person<personId -?[0-9]+>/]]]<action=default>',
            [
                'module' => 'Public',
                'subdomain' => '%subdomain.auth%',
                'tld' => '%domain.tld%',
                'contestId' => ['filterTable' => '%inverseContestMapping%'],
                'year' => null,
            ],
            [Route::SECURED]);


        $routeList[] = new Route('//%subdomain.db%.%domain.host%.[!<tld>]%path%[<contestId %contests%>/]<presenter register>/<action=default>',
            [
                'module' => 'Public',
                'subdomain' => '%subdomain.auth%',
                'tld' => '%domain.tld%',
                'contestId' => ['filterTable' => '%inverseContestMapping%'],
            ],
            [Route::ONE_WAY, Route::SECURED]);

        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%',
            [
                'subdomain' => '%subdomain.db%',
                'presenter' => 'Dispatch',
                'tld' => '%domain.tld%',
                'action' => 'default',
            ]
            [Route::SECURED]);

        # Application itself (note the 'presenter's w/out 'module' are handled specially)
        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%[<contestId %contests%>[<year [0-9]+>]/]<presenter %rootpresenters%>/<action=default>[/<id>]',
            [
                'subdomain' => '%subdomain.db%',
                'tld' => '%domain.tld%',
                'contestId' => ['filterTable' => '%inverseContestMapping%'],
            ],
            [Route::SECURED]);

        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%fyziklani[<eventId [0-9]+>]/<presenter>/<action=default>[/<id>]',
            [
                'subdomain' => '%subdomain.db%',
                'tld' => '%domain.tld%',
                'module' => 'Fyziklani',
            ], [Route::SECURED]);


        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%event[<eventId [0-9]+>]/<presenter>/<action=default>[/<id>]',
            [
                'subdomain' => '%subdomain.db%',
                'tld' => '%domain.tld%',
                'module' => 'Event',
                'presenter' => 'Dashboard']
            [Route::SECURED]);
        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%f[<eventId [0-9]+>]/s/q[/<id>]',
            [
                'subdomain' => '%subdomain.db%',
                'presenter' => 'Submit',
                'action' => 'qrEntry',
                'tld' => '%domain.tld %',
                'module' => 'Fyziklani',
            ], [Route::SECURED]);


        $routeList[] = new Route('//[!<subdomain>].%domain.host%.[!<tld>]%path%[<contestId %contests%>[<year [0-9]+>]/]<module %modules%>/<presenter>/<action=default>[/<id>]',
            [
                'presenter' => 'Dashboard',
                'subdomain' => '%subdomain.db%',
                'tld' => '%domain.tld%',
                'contestId' => ['filterTable' => '%inverseContestMapping%']]
            [Route::SECURED]);

        return $routeList;
    }
}
