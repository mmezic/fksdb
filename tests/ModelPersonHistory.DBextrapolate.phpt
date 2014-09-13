<?php

$container = require 'bootstrap.php';

use Nette\Database\Connection;
use Tester\Assert;

class ModelPersonHistoryTest extends DatabaseTestCase {

    /**
     * @var ServicePerson
     */
    private $service;

    /**
     * @var Connection
     */
    private $connection;

    function __construct(ServicePerson $service, Connection $connection) {
        parent::__construct($connection);
        $this->service = $service;
        $this->connection = $connection;
    }

    public function testNull() {
        $person = $this->service->findByPrimary(1);
        $extrapolated = $person->getHistory(2001, true);

        Assert::same(2001, $extrapolated->ac_year);
        Assert::same(1, $extrapolated->school_id);
        Assert::same(null, $extrapolated->class);
        Assert::same(2, $extrapolated->study_year);
    }

}

$testCase = new ModelPersonHistoryTest($container->getService('ServicePerson'), $container->getService('nette.database.default'));
$testCase->run();
