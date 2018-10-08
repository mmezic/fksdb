<?php

namespace FKSDB\Components\Forms\Controls\PersonAccommodation;

use FKSDB\ORM\ModelEventAccommodation;
use FKSDB\ORM\ModelPerson;
use Nette\ArrayHash;
use Nette\NotImplementedException;
use ServiceEventPersonAccommodation;
use Submits\StorageException;

class Handler {
    private $serviceEventPersonAccommodation;
    private $serviceEventAccommodation;

    public function __construct(ServiceEventPersonAccommodation $serviceEventPersonAccommodation, \ServiceEventAccommodation $serviceEventAccommodation) {
        $this->serviceEventPersonAccommodation = $serviceEventPersonAccommodation;
        $this->serviceEventAccommodation = $serviceEventAccommodation;
    }

    /**
     * @param ArrayHash $data
     * @param ModelPerson $person
     * @param integer $eventId
     * @throws StorageException
     * @return void
     */
    public function prepareAndUpdate(ArrayHash $data, ModelPerson $person, $eventId): void {
        $oldRows = $this->serviceEventPersonAccommodation->getTable()->where('person_id', $person->person_id)->where('event_accommodation.event_id', $eventId);

        $newAccommodationIds = $this->prepareData($data);
        /**
         * @var $row \FKSDB\ORM\ModelEventPersonAccommodation
         */
        foreach ($oldRows as $row) {
            if (in_array($row->event_accommodation_id, $newAccommodationIds)) {
                // do nothing
                $index = array_search($row->event_accommodation_id, $newAccommodationIds);
                unset($newAccommodationIds[$index]);
            } else {
                $row->delete();
            }
        }
        foreach ($newAccommodationIds as $id) {
            $model = $this->serviceEventPersonAccommodation->createNew(['person_id' => $person->person_id, 'event_accommodation_id' => $id]);
            $query = $this->serviceEventAccommodation->findByPrimary($id);
            $eventAccommodation = ModelEventAccommodation::createFromTableRow($query);
            if ($eventAccommodation->getAvailableCapacity() > 0) {
                $this->serviceEventPersonAccommodation->save($model);
            } else {
                //$model->delete();
                throw new StorageException(sprintf(
                    _('Osobu %s sa nepodarilo ubytovať na hotely "%s" v dni %s'),
                    $person->getFullName(),
                    $eventAccommodation->name,
                    $eventAccommodation->date->format(ModelEventAccommodation::ACC_DATE_FORMAT)
                ));

            }
        }
    }

    /**
     * @param ArrayHash $data
     * @return integer[]
     */
    private function prepareData(ArrayHash $data): array {
        foreach ($data as $type => $datum) {
            switch ($type) {
                case Matrix::RESOLUTION_ID:
                case Single::RESOLUTION_ID:
                case MultiHotels::RESOLUTION_ID:
                case MultiNights::RESOLUTION_ID:
                    $data = (array)json_decode($datum);
                    break;
                default:
                    throw new NotImplementedException(sprintf(_('Type "%s" is not implement.'), $type));
            }
        }

        return array_values($data);
    }
}

