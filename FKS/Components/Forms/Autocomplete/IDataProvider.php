<?php

namespace FKS\Components\Forms\Controls\Autocomplete;

/**
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
interface IDataProvider {

    const LABEL = 'label';
    const VALUE = 'value';

    /**
     * @return array array of associative arrays with at least LABEL and VALUE keys
     */
    public function getItems();

    /**
     * @param mixed $id
     */
    public function getItemLabel($id);
}

?>
