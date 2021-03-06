<?php

namespace FKSDB\Payment\PriceCalculator\PreProcess;

use FKSDB\ORM\ModelEventParticipant;
use FKSDB\ORM\ModelPayment;
use FKSDB\Payment\PriceCalculator\Price;
use Nette\NotImplementedException;

/**
 * Class EventPrice
 * @package FKSDB\Payment\PriceCalculator\PreProcess
 */
class EventPrice extends AbstractPreProcess {
    /**
     * @var \ServiceEventParticipant
     */
    private $serviceEventParticipant;

    /**
     * EventPrice constructor.
     * @param \ServiceEventParticipant $serviceEventParticipant
     */
    public function __construct(\ServiceEventParticipant $serviceEventParticipant) {
        $this->serviceEventParticipant = $serviceEventParticipant;
    }

    /**
     * @param ModelPayment $modelPayment
     * @return Price
     */
    public static function calculate(ModelPayment $modelPayment): Price {
       /* $price = new Price(0, $modelPayment->currency);
        $ids = $this->getData($modelPayment);
        foreach ($ids as $id) {
            $row = $this->serviceEventParticipant->findByPrimary($id);
            $model = ModelEventParticipant::createFromTableRow($row);
            $price->add($this->getPriceFromModel($model, $price));
        }*/
        return new Price(0, $modelPayment->currency);
    }

    /**
     * @param ModelPayment $modelPayment
     * @return array
     */
    public static function getGridItems(ModelPayment $modelPayment): array {
        /*$price = new Price(0, $modelPayment->currency);
        $items = [];
        $ids = $this->getData([]);
        foreach ($ids as $id) {
            $row = $this->serviceEventParticipant->findByPrimary($id);
            $model = ModelEventParticipant::createFromTableRow($row);
            $items[] = [
                'price' => $this->getPriceFromModel($model, $price),
                'label' => '',// TODO
            ];
        }
        return $items;*/
        return [];
    }

    /**
     * @param ModelEventParticipant $modelEventAccommodation
     * @param Price $price
     * @return Price
     */
    private function getPriceFromModel(ModelEventParticipant $modelEventAccommodation, Price $price): Price {
        switch ($price->getCurrency()) {
            case Price::CURRENCY_KC:
                $amount = $modelEventAccommodation->price;
                break;
            default:
                throw new NotImplementedException(\sprintf(_('Mena %s nieje implentovaná'), $price->getCurrency()), 501);
        }
        return new Price($amount, $price->getCurrency());
    }
}
