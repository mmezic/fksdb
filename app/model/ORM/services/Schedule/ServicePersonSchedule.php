<?php


namespace FKSDB\ORM\Services\Schedule;

/**
 * Class ServicePersonSchedule
 * @package FKSDB\ORM\Services\Schedule
 */
class ServicePersonSchedule extends \AbstractServiceSingle {
    protected $tableName = \DbNames::TAB_PERSON_SCHEDULE;
    protected $modelClassName = 'FKSDB\ORM\Models\Schedule\ModelPersonSchedule';
}
