<?php

namespace FKSDB\model\Fyziklani;

use FKSDB\Utils\CSVParser;
use FyziklaniModule\TaskPresenter;
use ServiceFyziklaniTask;
use \Nette\Diagnostics\Debugger;

/**
 *
 * @author Michal Červeňák
 * @author Lukáš Timko
 */
class FyziklaniTaskImportProcessor {

    /**
     *
     * @var ServiceFyziklaniTask
     */
    private $serviceFyziklaniTask;
    /**
     * @var integer
     */
    private $eventId;

    public function __construct($eventId, ServiceFyziklaniTask $serviceFyziklaniTask) {
        $this->eventId = $eventId;
        $this->serviceFyziklaniTask = $serviceFyziklaniTask;
    }

    public function __invoke($values, &$messages) {
        $filename = $values->csvfile->getTemporaryFile();
        $connection = $this->serviceFyziklaniTask->getConnection();
        $connection->beginTransaction();
        if ($values->state == TaskPresenter::IMPORT_STATE_REMOVE_N_INSERT) {
            $this->serviceFyziklaniTask->findAll($this->eventId)->delete();
        }
        $parser = new CSVParser($filename, CSVParser::INDEX_FROM_HEADER);
        foreach ($parser as $row) {
            try {
                $task = $this->serviceFyziklaniTask->findByLabel($row['label'], $this->eventId);
                if (!$task) {
                    $task = $this->serviceFyziklaniTask->createNew([
                        'label' => $row['label'],
                        'name' => $row['name'],
                        'event_id' => $this->eventId
                    ]);
                    $messages[] = [sprintf(_('Úloha %s "%s" bola vložena'), $row['label'], $row['name']), 'success'];
                } elseif ($values->state == TaskPresenter::IMPORT_STATE_UPDATE_N_INSERT) {
                        $this->serviceFyziklaniTask->updateModel($task, [
                            'label' => $row['label'],
                            'name' => $row['name']
                        ]);
                        $messages[] = [sprintf(_('Úloha %s "%s" byla aktualizována'), $row['label'], $row['name']), 'info'];
                } else {
                        $messages[] = [
                            sprintf(_('Úloha %s "%s" nebyla aktualizována'), $row['label'], $row['name']),
                            'warning'
                        ];
                }
                $this->serviceFyziklaniTask->save($task);
            } catch (\Exception $e) {
                $messages[] = [_('Vyskytla se chyba'), 'danger'];
                Debugger::log($e);
                $connection->rollBack();
                return;
            }
        }
        $connection->commit();
    }
}
