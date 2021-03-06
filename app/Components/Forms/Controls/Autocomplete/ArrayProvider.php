<?php

namespace FKSDB\Components\Forms\Controls\Autocomplete;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ArrayProvider implements IFilteredDataProvider {

    private $data;
    private $labelById;

    /**
     * ArrayProvider constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->data = [];
        $this->labelById = $data;
        foreach ($data as $id => $label) {
            $this->data[] = array(
                self::VALUE => $id,
                self::LABEL => $label,
            );
        }
    }

    /**
     * Prefix search.
     *
     * @param string $search
     * @return array
     */
    public function getFilteredItems($search) {
        $result = [];
        foreach ($this->data as $item) {
            $label = $item[self::LABEL];
            if (mb_substr($label, 0, mb_strlen($search)) == $search) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function getItemLabel($id) {
        return $this->labelById[$id];
    }

    /**
     * @return array
     */
    public function getItems() {
        return $this->data;
    }

    /**
     * @param $id
     */
    public function setDefaultValue($id) {
        /* intentionally blank */
    }


//put your code here
}
