<?php

namespace Persons\Deduplication\MergeStrategy;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class TrunkStrategy implements IMergeStrategy {

    public function mergeValues($trunk, $merged) {
        return $trunk;
    }

}
