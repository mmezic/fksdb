<?php


namespace FKSDB\Components\Controls\Stalking\Helpers;


use FKSDB\ORM\ModelEvent;
use Nette\Application\UI\Control;

/**
 * Class EventLabelControl
 * @package FKSDB\Components\Controls\Stalking\Helpers
 */
class EventLabelControl extends Control {
    /**
     * @param ModelEvent $event
     */
    public function render(ModelEvent $event) {
        $this->template->event = $event;
        $this->template->setFile(__DIR__ . '/EventLabelControl.latte');
        $this->template->render();
    }
}
