<?php

namespace FKSDB\Components\Controls\Transitions;

use FKSDB\Transitions\IStateModel;
use FKSDB\Transitions\Machine;
use FKSDB\Transitions\UnavailableTransitionException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Control;
use Nette\Diagnostics\Debugger;
use Nette\Localization\ITranslator;
use Nette\Templating\FileTemplate;

/**
 * Class TransitionButtonsControl
 * @package FKSDB\Components\Controls\Transitions
 * @property FileTemplate $template
 */
class TransitionButtonsControl extends Control {
    /**
     * @var Machine
     */
    private $machine;
    /**
     * @var ITranslator
     */
    private $translator;
    /**
     * @var IStateModel
     */
    private $model;

    /**
     * TransitionButtonsControl constructor.
     * @param Machine $machine
     * @param ITranslator $translator
     * @param IStateModel $model
     */
    public function __construct(Machine $machine, ITranslator $translator, IStateModel $model) {
        parent::__construct();
        $this->machine = $machine;
        $this->translator = $translator;
        $this->model = $model;
    }

    public function render() {
        $this->template->buttons = $this->machine->getAvailableTransitions($this->model);
        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'TransitionButtonsControl.latte');
        $this->template->render();
    }

    /**
     * @param $name
     * @throws \Nette\Application\AbortException
     */
    public function handleTransition($name) {
        try {
            $this->machine->executeTransition($name, $this->model);
        } catch (ForbiddenRequestException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), \BasePresenter::FLASH_ERROR);
            return;
        } catch (UnavailableTransitionException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), \BasePresenter::FLASH_ERROR);
            return;
        } catch (\Exception $e) {
            Debugger::barDump($e);
            Debugger::log($e);
            $this->getPresenter()->flashMessage(_('Nastala chyba'), \BasePresenter::FLASH_ERROR);
        }
        $this->redirect('this');
    }
}
