<?php


namespace FKSDB\ORM\Services\Schedule;

/**
 * Class ServiceSchedulePayment
 * @package FKSDB\ORM\Services\Schedule
 */
class ServiceSchedulePayment extends \AbstractServiceSingle {

    /**
     * @return string
     */
    protected function getTableName(): string {
        return \DbNames::TAB_SCHEDULE_ITEM;
    }

    /**
     * @return string
     */
    protected function getModelClassName(): string {
        return 'FKSDB\ORM\Models\Schedule\ModelScheduleItem';
    }
}
