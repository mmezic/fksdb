<?php

namespace Exports;

use Nette\Application\IResponse;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
interface IExportFormat {

    /**
     * @return IResponse
     */
    public function getResponse();
}
