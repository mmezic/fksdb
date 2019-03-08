<?php

namespace FKSDB\Components\Controls\Fyziklani\Submit;

use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;
use Nette\Templating\FileTemplate;

/**
 * Class DetailControl
 * @property FileTemplate $template
 */
class DetailControl extends Control {
    /**
     * @var \ModelFyziklaniSubmit
     */
    private $model;
    /**
     * @var ITranslator
     */
    private $translator;
    /**
     * @var \ServiceFyziklaniSubmit
     */
    private $serviceFyziklaniSubmit;

    /**
     * DetailControl constructor.
     * @param ITranslator $translator
     * @param \ServiceFyziklaniSubmit $serviceFyziklaniSubmit
     */
    public function __construct(ITranslator $translator, \ServiceFyziklaniSubmit $serviceFyziklaniSubmit) {
        parent::__construct();
        $this->translator = $translator;
        $this->serviceFyziklaniSubmit = $serviceFyziklaniSubmit;
    }

    /**
     * @param \ModelFyziklaniSubmit $submit
     */
    public function setSubmit(\ModelFyziklaniSubmit $submit) {
        $this->model = $submit;
    }

    public function render() {
        $this->template->model = $this->model;
        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'DetailControl.latte');
        $this->template->render();
    }

    public function handleCheck() {
        $submit = $this->model;

        $this->serviceFyziklaniSubmit->updateModel($submit, [
            'state' => \ModelFyziklaniSubmit::STATE_CHECKED,
            /* ugly, exclude previous value of `modified` from query
             * so that `modified` is set automatically by DB
             * see https://dev.mysql.com/doc/refman/5.5/en/timestamp-initialization.html
             */
            'checked_by' => $this->getPresenter()->getUser()->getIdentity()->getPerson()->person_id,
            'modified' => null
        ]);
        $this->serviceFyziklaniSubmit->save($submit);
        $this->getPresenter()->flashMessage(_('Submit has been checked.'), \BasePresenter::FLASH_SUCCESS);
        $this->redirect('this');
    }
}
