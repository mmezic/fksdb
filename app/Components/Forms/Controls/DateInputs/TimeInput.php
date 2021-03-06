<?php


namespace FKSDB\Components\Forms\Controls\DateInputs;

/**
 * Class TimeInput
 * @package FKSDB\Components\Forms\Controls\DateInputs
 */
class TimeInput extends AbstractDateInput {

    /**
     * @return string
     */
    protected function getFormat(): string {
        return 'H:i:s';
    }

    /**
     * @return string
     */
    protected function getType(): string {
        return 'time';
    }
}
