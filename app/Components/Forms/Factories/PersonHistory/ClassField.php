<?php

namespace FKSDB\Components\Forms\Factories\PersonHistory;

use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;

/**
 * Class ClassField
 * @package FKSDB\Components\Forms\Factories\PersonHistory
 */
class ClassField extends TextInput {

    public function __construct() {
        parent::__construct(_('Třída'));
        $this->addRule(Form::MAX_LENGTH, null, 16);
    }
}
