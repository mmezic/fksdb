<?php

namespace FKSDB\Components\View;

use Authorization\ContestAuthorizator;
use FKSDB\Components\Forms\Factories\StoredQueryFactory;
use FKSDB\Components\Grids\StoredQueryGrid;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use PDOException;
use SQL\StoredQuery;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class StoredQueryComponent extends Control {

    const CONT_PARAMS = 'params';

    /**
     * @persistent
     * @var array
     */
    public $parameters;

    /**
     * @var StoredQuery
     */
    private $storedQuery;

    /**
     * @var ContestAuthorizator
     */
    private $contestAuthorizator;

    /**
     * @var StoredQueryFactory
     */
    private $storedQueryFormFactory;

    /**
     * @var null|bool|string
     */
    private $error;

    /**
     * @var boolean
     */
    private $showParametrize = true;

    function __construct(StoredQuery $storedQuery, ContestAuthorizator $contestAuthorizator, StoredQueryFactory $storedQueryFormFactory) {
        $this->storedQuery = $storedQuery;
        $this->contestAuthorizator = $contestAuthorizator;
        $this->storedQueryFormFactory = $storedQueryFormFactory;
    }

    public function getShowParametrize() {
        return $this->showParametrize;
    }

    public function setShowParametrize($showParametrize) {
        $this->showParametrize = $showParametrize;
    }

    protected function createComponentGrid($name) {
        $grid = new StoredQueryGrid($this->storedQuery);
        return $grid;
    }

    protected function createComponentParametrizeForm($name) {
        $form = new Form();

        $queryPattern = $this->storedQuery->getQueryPattern();
        $parameters = $this->storedQueryFormFactory->createParametersValues($queryPattern);
        $form->addComponent($parameters, self::CONT_PARAMS);

        $form->addSubmit('execute', 'Parametrizovat');
        $form->onSuccess[] = function(Form $form) {
                    $this->parameters = array();
                    $values = $form->getValues();
                    foreach ($values[self::CONT_PARAMS] as $key => $values) {
                        $this->parameters[$key] = $values['value'];
                    }
                };

        return $form;
    }

    public function getSqlError() {
        if ($this->error === null) {
            $this->error = false;
            try {
                $this->storedQuery->getColumnNames(); // this may throw PDOException in the main query
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
        }
        return $this->error;
    }

    public function canParametrize() {
        $query = $this->storedQuery->getQueryPattern();
        $presenter = $this->getPresenter();
        return count($query->getParameters()) && $this->contestAuthorizator->isAllowed($query, 'parametrize', $presenter->getSelectedContest()); //TODO is it correct to read the property from the presenter here?
    }

    public function render() {
        if ($this->parameters) {
            $this->storedQuery->setParameters($this->parameters);
            $defaults = array();
            foreach ($this->parameters as $key => $value) {
                $defaults[$key] = array('value' => $value);
            }
            $defaults = array(self::CONT_PARAMS => $defaults);
            $this['parametrizeForm']->setDefaults($defaults);
        }

        $this->template->error = $this->getSqlError();
        $this->template->parametrize = $this->getShowParametrize() && $this->canParametrize();

        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'StoredQueryComponent.latte');
        $this->template->render();
    }

}
