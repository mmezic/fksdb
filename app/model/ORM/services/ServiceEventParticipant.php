<?php

use FKSDB\ORM\ModelEventParticipant;
use ORM\IModel;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServiceEventParticipant extends AbstractServiceSingle {

    protected $tableName = DbNames::TAB_EVENT_PARTICIPANT;
    protected $modelClassName = 'FKSDB\ORM\ModelEventParticipant';

    /**
     * @param IModel $model
     */
    public function save(IModel &$model) {
        try {
            parent::save($model);
        } catch (ModelException $e) {
            if ($e->getPrevious() && $e->getPrevious()->getCode() == 23000) {
                throw new DuplicateApplicationException($model->getPerson(), $e);
            }
            throw $e;
        }
    }

    /**
     * @param IModel $model
     * @param array $data
     * @param bool $alive
     * @return mixed|void
     */
    public function updateModel(IModel $model, $data, $alive = true) {
        /**
         * @var ModelEventParticipant $model
         */
        parent::updateModel($model, $data, $alive);
        if (!$alive && !$model->isNew()) {
            $person = $model->getPerson();
            if ($person) {
                $person->removeAccommodationForEvent($model->event_id);
            }

        }
    }
}
