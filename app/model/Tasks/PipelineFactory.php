<?php

namespace Tasks;

use FKSDB\Logging\MemoryLogger;
use Pipeline\Pipeline;
use ServiceOrg;
use ServiceStudyYear;
use ServiceTask;
use ServiceTaskContribution;
use ServiceTaskStudyYear;
use Tasks\Legacy\ContributionsFromXML;
use Tasks\Legacy\DeadlineFromXML;
use Tasks\Legacy\StudyYearsFromXML;
use Tasks\Legacy\TasksFromXML;

/**
 * This is not real factory, it's only used as an internode for defining
 * pipelines inside Neon and inject them into presenters at once.
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class PipelineFactory {

    /**
     * @see TasksFromXML
     * @var array
     */
    private $columnMappings;

    /**
     * @see ContributionsFromXML
     * @var array
     */
    private $contributionMappings;

    /**
     * @see StudyYearsFromXML
     * @var array
     */
    private $defaultStudyYears;

    /**
     * @var ServiceTask
     */
    private $serviceTask;

    /**
     * @var ServiceTaskContribution
     */
    private $serviceTaskContribution;

    /**
     * @var ServiceTaskStudyYear
     */
    private $serviceTaskStudyYear;

    /**
     * @var ServiceStudyYear
     */
    private $serviceStudyYear;

    /**
     * @var ServiceOrg
     */
    private $serviceOrg;

    /**
     * PipelineFactory constructor.
     * @param $columnMappings
     * @param $contributionMappings
     * @param $defaultStudyYears
     * @param ServiceTask $serviceTask
     * @param ServiceTaskContribution $serviceTaskContribution
     * @param ServiceTaskStudyYear $serviceTaskStudyYear
     * @param ServiceStudyYear $serviceStudyYear
     * @param ServiceOrg $serviceOrg
     */
    function __construct($columnMappings, $contributionMappings, $defaultStudyYears, ServiceTask $serviceTask, ServiceTaskContribution $serviceTaskContribution, ServiceTaskStudyYear $serviceTaskStudyYear, ServiceStudyYear $serviceStudyYear, ServiceOrg $serviceOrg) {
        $this->columnMappings = $columnMappings;
        $this->contributionMappings = $contributionMappings;
        $this->defaultStudyYears = $defaultStudyYears;
        $this->serviceTask = $serviceTask;
        $this->serviceTaskContribution = $serviceTaskContribution;
        $this->serviceTaskStudyYear = $serviceTaskStudyYear;
        $this->serviceStudyYear = $serviceStudyYear;
        $this->serviceOrg = $serviceOrg;
    }

    /**
     *
     * @param $language
     * @return \Pipeline\Pipeline
     */
    public function create($language) {
        $pipeline = new Pipeline();
        $pipeline->setLogger(new MemoryLogger());


        // common stages
        $metadataStage = new TasksFromXML($this->columnMappings[$language], $this->serviceTask);
        $pipeline->addStage($metadataStage);

        if ($language == 'cs') {
            $deadlineStage = new DeadlineFromXML($this->serviceTask);
            $pipeline->addStage($deadlineStage);

            $contributionStage = new ContributionsFromXML($this->contributionMappings, $this->serviceTaskContribution, $this->serviceOrg);
            $pipeline->addStage($contributionStage);

            $studyYearStage = new StudyYearsFromXML($this->defaultStudyYears, $this->serviceTaskStudyYear, $this->serviceStudyYear);
            $pipeline->addStage($studyYearStage);
        }

        return $pipeline;
    }

    /**
     *
     * @return \Pipeline\Pipeline
     */
    public function create2() {
        $pipeline = new Pipeline();
        $pipeline->setLogger(new MemoryLogger());

        // common stages
        $metadataStage = new TasksFromXML2($this->serviceTask);
        $pipeline->addStage($metadataStage);

        $deadlineStage = new DeadlineFromXML2($this->serviceTask);
        $pipeline->addStage($deadlineStage);

        $contributionStage = new ContributionsFromXML2($this->serviceTaskContribution, $this->serviceOrg);
        $pipeline->addStage($contributionStage);

        $studyYearStage = new StudyYearsFromXML2($this->defaultStudyYears, $this->serviceTaskStudyYear, $this->serviceStudyYear);
        $pipeline->addStage($studyYearStage);

        return $pipeline;
    }

}
