<?php

use FKSDB\ORM\ModelPerson;
use Nette\Database\Table\ActiveRow;
use Nette\DateTime;
use ORM\Models\Events\ModelFyziklaniTeam;

/**
 *
 * @author Lukáš Timko <lukast@fykos.cz>
 * @author Michal Červeňák <miso@fykos.cz>
 * @property integer fyziklani_submit_id
 * @property integer e_fyziklani_team_id
 * @property ActiveRow e_fyziklani_team
 * @property integer fyziklani_task_id
 * @property ActiveRow fyziklani_task
 *
 * @property integer points
 * @property string state
 * @property int checked_by
 * @property int created_by
 *
 * @property DateTime created
 * @property DateTime modified
 */
class ModelFyziklaniSubmit extends \AbstractModelSingle {
    const STATE_NOT_CHECKED = 'not_checked';
    const STATE_CHECKED = 'checked';

    /**
     * @return ModelFyziklaniTask
     */
    public function getTask(): ModelFyziklaniTask {
        return ModelFyziklaniTask::createFromTableRow($this->fyziklani_task);
    }

    /**
     * @return ModelFyziklaniTeam
     */
    public function getTeam(): ModelFyziklaniTeam {
        return ModelFyziklaniTeam::createFromTableRow($this->e_fyziklani_team);
    }

    /**
     * @return ModelPerson|null
     */
    public function getCreatedBy() {
        $row = $this->ref(DbNames::TAB_PERSON, 'created_by');
        if (!$row) {
            return null;
        }
        return ModelPerson::createFromTableRow($row);
    }

    /**
     * @return bool
     */
    public function isChecked(): bool {
        return $this->state === self::STATE_CHECKED;
    }

    /**
     * @return ModelPerson|null
     */
    public function getCheckedBy() {
        $row = $this->ref(DbNames::TAB_PERSON, 'checked_by');
        if (!$row) {
            return null;
        }
        return ModelPerson::createFromTableRow($row);
    }

    /**
     * @return array
     */
    public function __toArray(): array {
        return [
            'points' => $this->points,
            'teamId' => $this->e_fyziklani_team_id,
            'taskId' => $this->fyziklani_task_id,
            'created' => $this->created->format('c'),
        ];
    }
}
