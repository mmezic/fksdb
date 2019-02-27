<?php


namespace FKSDB\ORM\Models\Schedule;

use FKSDB\ORM\ModelPerson;
use FKSDB\Transitions\IStateModel;
use Nette\Database\Table\ActiveRow;
use Nette\NotImplementedException;

/**
 * Class ModelPersonSchedule
 * @package FKSDB\ORM\Models\Schedule
 * @property ActiveRow person
 * @property ActiveRow schedule_item
 * @property int person_id
 * @property int schedule_item_id
 * @property string state
 */
class ModelPersonSchedule extends \AbstractModelSingle implements IStateModel {
    /**
     * @return ModelPerson
     */
    public function getPerson(): ModelPerson {
        return ModelPerson::createFromTableRow($this->person);
    }

    /**
     * @return ModelScheduleItem
     */
    public function getScheduleItem(): ModelScheduleItem {
        return ModelScheduleItem::createFromTableRow($this->schedule_item);
    }

    /**
     * @param $newState
     * @return mixed|void
     */
    public function updateState($newState) {
        $this->update(['state' => $newState]);
    }

    /**
     * @return null|string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @return IStateModel
     */
    public function refresh(): IStateModel {
        throw new NotImplementedException();
    }

}
