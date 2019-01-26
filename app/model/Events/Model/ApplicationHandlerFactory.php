<?php

namespace Events\Model;

use FKSDB\Logging\ILogger;
use FKSDB\ORM\ModelEvent;
use Nette\Database\Connection;
use Nette\DI\Container;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ApplicationHandlerFactory {

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Container
     */
    private $container;

    function __construct(Connection $connection, Container $container) {
        $this->connection = $connection;
        $this->container = $container;
    }

    public function create(ModelEvent $event, ILogger $logger) {
        return new ApplicationHandler($event, $logger, $this->connection, $this->container);
    }

}
