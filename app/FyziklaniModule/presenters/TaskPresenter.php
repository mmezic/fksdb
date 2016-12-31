<?php

namespace FyziklaniModule;

use FKSDB\model\Fyziklani\FyziklaniTaskImportProcessor;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use \Nette\Application\UI\Form;
use \FKSDB\Components\Grids\Fyziklani\FyziklaniTaskGrid;


class TaskPresenter extends BasePresenter {

    const EVENT_TYPE_ID = 1;
    const IMPORT_STATE_UPDATE_N_INSERT = 1;
    const IMPORT_STATE_REMOVE_N_INSERT = 2;
    const IMPORT_STATE_INSERT = 3;

    public function titleTable() {
        $this->setTitle(_('Úlohy Fykosího Fyzikláni'));
    }

    public function authorizedTable() {
        $this->setAuthorized($this->getEventAuthorizator()->isAllowed('fyziklani', 'task', $this->getCurrentEvent()));
    }

    public function titleImport() {
        $this->setTitle(_('Import úloh Fykosího Fyzikláni'));
    }

    public function authorizedImport() {
        $this->setAuthorized($this->getContestAuthorizator()->isAllowed('fyziklani', 'taskImport', $this->getCurrentEvent()->event_type->contest_id));
    }

    public function createComponentTaskImportForm() {
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer);
        $form->addUpload('csvfile')->setRequired();
        $form->addSelect('state', _('Vyberte akciu'), [
            self::IMPORT_STATE_UPDATE_N_INSERT => _('Updatnuť úlohy a pridať ak neexistuje'),
            self::IMPORT_STATE_REMOVE_N_INSERT => _('Ostrániť všetky úlohy a nahrať nove'),
            self::IMPORT_STATE_INSERT => _('Pridať ak neexistuje')
        ]);
        $form->addSubmit('import', _('Importovať'));
        $form->onSuccess[] = [$this, 'taskImportFormSucceeded'];
        return $form;
    }

    public function taskImportFormSucceeded(Form $form) {
        $values = $form->getValues();
        $taskImportProcessor = new FyziklaniTaskImportProcessor($this->eventID, $this->serviceFyziklaniTask);
        $messages = [];
        $taskImportProcessor($values, $messages);
        foreach ($messages as $message) {
            $this->flashMessage($message[0], $message[1]);
        }
        $this->redirect('this');
    }

    public function createComponentTaskGrid() {
        return new FyziklaniTaskGrid($this->eventID, $this->serviceFyziklaniTask);
    }
}
