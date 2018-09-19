<?php

namespace FKSDB\Components\Controls;

use Nette\Application\PresenterFactory;
use Nette\Application\UI\Presenter;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class PresenterBuilder {

    /**
     * @var PresenterFactory
     */
    private $presenterFactory;
    private $presenterCache = array();

    function __construct(PresenterFactory $presenterFactory) {
        $this->presenterFactory = $presenterFactory;
    }

    /**
     * Provides an instance of a presenter prepared for calling action*, render*, etc. methods.
     *
     * @param string $presenterName
     * @param string $action
     * @param string $params
     * @param string $baseParams
     * @param boolean $newInstance when false all instances of the same class will be the same and only initilization methods are called
     * @return Presenter
     */
    public function preparePresenter($presenterName, $action, $params, $baseParams = array(), $newInstance = false) {
        if ($newInstance) {
            $presenter = $this->presenterFactory->createPresenter($presenterName);
        } else {
            $presenter = $this->getCachePresenter($presenterName);
        }

        $params = $params ?: array();

        unset($baseParams[Presenter::ACTION_KEY]);
        foreach ($params as $key => $value) {
            $baseParams[$key] = $value;
        }
        $presenter->loadState($baseParams);
        $presenter->changeAction($action);

        return $presenter;
    }

    /**
     * @param string $presenterName
     * @return Presenter
     */
    private function getCachePresenter($presenterName) {
        if (!isset($this->presenters[$presenterName])) {
            $this->presenterCache[$presenterName] = $this->presenterFactory->createPresenter($presenterName);
        }
        return $this->presenterCache[$presenterName];
    }

}
