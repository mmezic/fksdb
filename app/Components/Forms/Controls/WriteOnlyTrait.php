<?php

namespace FKSDB\Components\Forms\Controls;

use FKS\Application\IJavaScriptCollector;
use Nette\Forms\Form;
use Nette\Utils\Html;

/**
 * When user doesn't fill it (i.e. desires original value), it behaves like disabled.
 * Only FILLED validation works properly because there's used special value to distinguish unchanged input.
 *
 * @note When using this trait, be sure to call:
 *           writeonlyAppendMonitors in __constuctor
 *           writeonlyAdjustControl in getControl
 *           writeonlyLoadHttpData in loadHttpData after original loadHttpData
 *       and writeonlyAttached in attached.
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
trait WriteOnlyTrait {

    private $writeonly = true;
    private $actuallyDisabled = false;
    private $hasManualValue = false;

    private function writeonlyAppendMonitors() {
        $this->monitor('Nette\Forms\Form');
        $this->monitor('FKS\Application\IJavaScriptCollector');
    }

    public function getWriteOnly() {
        return $this->writeonly;
    }

    public function setWriteOnly($writeonly = true) {
        $this->writeonly = $writeonly;
    }

    private function writeonlyAdjustControl(Html $control) {
// rendered control may not disabled
        $control->disabled = $this->actuallyDisabled;

// don't show the value (only if it's form displayed after submit)
// for JS
        if ($this->writeonly && $this->getValue() && !$this->hasManualValue) {
            $control->data['writeonly'] = (int) true;
            $control->data['writeonly-value'] = self::VALUE_ORIGINAL;
            $control->data['writeonly-label'] = _('skrytá hodnota');
            $control->value = self::VALUE_ORIGINAL;
        }
        return $control;
    }

    protected function writeonlyLoadHttpData() {
        if ($this->getValue() != self::VALUE_ORIGINAL) {
            $this->hasManualValue = true;
        }
    }

    private function writeonlyDisable() {
        $this->actuallyDisabled = $this->isDisabled();
        $this->setDisabled();
    }

    private $writeonlyAttachedOnValidate = false;
    private $writeonlyAttachedJS = false;

    protected function writeonlyAttached($obj) {
        parent::attached($obj);
        if (!$this->writeonlyAttachedOnValidate && $obj instanceof Form) {
            $that = $this;
            $obj->onValidate = $obj->onValidate ? : array();
            array_unshift($obj->onValidate, function(Form $form) use($that) {
                        if ($that->writeonly && $that->getValue() == self::VALUE_ORIGINAL) {
                            $that->writeonlyDisable();
                        }
                    });
            $this->writeonlyAttachedOnValidate = true;
        }
        if (!$this->writeonlyAttachedJS && $obj instanceof IJavaScriptCollector) {
            $this->writeonlyAttachedJS = true;
            $obj->registerJSFile('js/writeonlyInput.js');
        }
    }

}