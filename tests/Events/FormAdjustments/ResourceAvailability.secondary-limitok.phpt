<?php

namespace Events\Model;

use Nette\Application\Request;
use Tester\Assert;
use Tester\DomQuery;

$container = require '../../bootstrap.php';

class ResourceAvailabilityTest extends ResourceAvailabilityTestCase {

    private $tsafEventId;

    protected function setUp() {
        parent::setUp();
        $this->tsafEventId = $this->createEvent(array(
            'event_type_id' => 7,
            'event_year' => 7,
            'parameters' => <<<EOT
EOT
        ));


        foreach ($this->persons as $personId) {
            $eid = $this->insert('event_participant', array(
                'person_id' => $personId,
                'event_id' => $this->tsafEventId,
                'status' => 'applied',
                'accomodation' => 1,
            ));
            $this->insert('e_tsaf_participant', array(
                'event_participant_id' => $eid,
            ));
        }
    }

    protected function tearDown() {
        $this->connection->query("DELETE FROM e_tsaf_participant");
        parent::tearDown();
    }

    public function getTestData() {
        return array(
            array(3, false),
            array(2, true),
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testDisplay($capacity, $diabled) {
        Assert::equal('2', $this->connection->query('SELECT SUM(accomodation) FROM event_participant WHERE event_id = ?', $this->eventId)->fetchColumn());

        $this->connection->query('UPDATE event SET parameters = ? WHERE event_id = ?', <<<EOT
accomodationCapacity: $capacity                
EOT
                , $this->eventId);

        $request = new Request('Public:Application', 'GET', array(
            'action' => 'default',
            'lang' => 'cs',
            'contestId' => 1,
            'year' => 1,
            'eventId' => $this->tsafEventId,
        ));

        $response = $this->fixture->run($request);
        Assert::type('Nette\Application\Responses\TextResponse', $response);

        $source = $response->getSource();
        Assert::type('Nette\Templating\ITemplate', $source);

        $html = (string) $source;
        $dom = DomQuery::fromHtml($html);
        Assert::true((bool) $dom->xpath('//input[@name="participantDsef[accomodation]"]'));
        Assert::equal($diabled, (bool) $dom->xpath('//input[@name="participantDsef[accomodation]"][@disabled="disabled"]'));
    }

    public function getCapacity() {
        return 3;
    }

}

$testCase = new ResourceAvailabilityTest($container);
$testCase->run();
