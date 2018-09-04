<?php

namespace FKSDB\Components\Forms\Factories\PersonInfo;

use FKS\Components\Forms\Controls\WriteonlyInput;
use Nette\Forms\Form;

class UkLoginField extends WriteonlyInput {


    public function __construct() {
        parent::__construct(_('Login UK'));
        $this->addRule(Form::MAX_LENGTH, null, 8);
    }
}