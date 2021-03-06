<?php

namespace Submits;

use FKSDB\ORM\ModelContest;
use FKSDB\ORM\ModelContestant;
use FKSDB\ORM\ModelSubmit;
use Nette\Database\Table\Selection;
use ServiceContestant;
use ServiceSubmit;
use ServiceTask;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @todo Prominent example for necessity of caching.
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class SeriesTable {

    const FORM_SUBMIT = 'submit';
    const FORM_CONTESTANT = 'contestant';

    /**
     * @var ServiceContestant
     */
    private $serviceContestant;

    /**
     * @var ServiceTask
     */
    private $serviceTask;

    /**
     * @var ServiceSubmit
     */
    private $serviceSubmit;

    /**
     * @var ModelContest
     */
    private $contest;

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $series;

    /**
     *
     * @var null|array of int IDs of allowed tasks or null for unrestricted
     */
    private $taskFilter;

    /**
     * SeriesTable constructor.
     * @param ServiceContestant $serviceContestant
     * @param ServiceTask $serviceTask
     * @param ServiceSubmit $serviceSubmit
     */
    function __construct(ServiceContestant $serviceContestant, ServiceTask $serviceTask, ServiceSubmit $serviceSubmit) {
        $this->serviceContestant = $serviceContestant;
        $this->serviceTask = $serviceTask;
        $this->serviceSubmit = $serviceSubmit;
    }

    /**
     * @return ModelContest
     */
    public function getContest() {
        return $this->contest;
    }

    /**
     * @param ModelContest $contest
     */
    public function setContest(ModelContest $contest) {
        $this->contest = $contest;
    }

    /**
     * @return int
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * @param $year
     */
    public function setYear($year) {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getSeries() {
        return $this->series;
    }

    /**
     * @param $series
     */
    public function setSeries($series) {
        $this->series = $series;
    }

    /**
     * @return array|null
     */
    public function getTaskFilter() {
        return $this->taskFilter;
    }

    /**
     * @param $taskFilter
     */
    public function setTaskFilter($taskFilter) {
        $this->taskFilter = $taskFilter;
    }

    /**
     * @return Selection
     */
    public function getContestants(): Selection {
        return $this->serviceContestant->getTable()->where([
            'contest_id' => $this->getContest()->contest_id,
            'year' => $this->getYear(),
        ])->order('person.family_name, person.other_name, person.person_id');
        //TODO series
    }

    /**
     * @return Selection
     */
    public function getTasks(): Selection {
        $tasks = $this->serviceTask->getTable()->where([
            'contest_id' => $this->getContest()->contest_id,
            'year' => $this->getYear(),
            'series' => $this->getSeries(),
        ]);

        if ($this->getTaskFilter() !== null) {
            $tasks->where('task_id', $this->getTaskFilter());
        }
        return $tasks->order('tasknr');
    }

    /**
     * @return array
     */
    public function getSubmitsTable(): array {
        $submits = $this->serviceSubmit->getTable()
            ->where('ct_id', $this->getContestants())
            ->where('task_id', $this->getTasks());

        // store submits in 2D hash for better access
        $submitsTable = [];
        foreach ($submits as $row) {
            $submit = ModelSubmit::createFromTableRow($row);
            if (!isset($submitsTable[$submit->ct_id])) {
                $submitsTable[$submit->ct_id] = [];
            }
            $submitsTable[$submit->ct_id][$submit->task_id] = $submit;
        }
        return $submitsTable;
    }

    /**
     * @return array
     */
    public function formatAsFormValues() {
        $submitsTable = $this->getSubmitsTable();
        $contestants = $this->getContestants();
        $result = [];
        foreach ($contestants as $contestantRow) {
            $contestant = ModelContestant::createFromTableRow($contestantRow);
            $ctId = $contestant->ct_id;
            if (isset($submitsTable[$ctId])) {
                $result[$ctId] = [self::FORM_SUBMIT => $submitsTable[$ctId]];
            } else {
                $result[$ctId] = [self::FORM_SUBMIT => null];
            }
        }
        return [
            self::FORM_CONTESTANT => $result
        ];
    }

    /**
     * @return array
     */
    public function getFingerprint() {
        $fingerprint = '';
        foreach ($this->getSubmitsTable() as $submits) {
            foreach ($submits as $submit) {
                /**
                 * @var ModelSubmit $submit
                 */
                $fingerprint .= $submit->getFingerprint();
            }
        }
        return md5($fingerprint);
    }

}
