<?php

namespace FKSDB\Components\Forms\Factories;

use FKSDB\Components\Forms\Containers\ModelContainer;
use Nette\Forms\ControlGroup;
use Nette\Forms\Form;
use ServiceContest;
use ServiceSchool;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class ContestantFactory {

    const REQUIRE_SCHOOL = 0x1;
    const REQUIRE_STUDY_YEAR = 0x2;
    const SHOW_CONTEST = 0x4;

    /**
     * @var ServiceSchool
     */
    private $serviceSchool;

    /**
     * @var SchoolFactory
     */
    private $factorySchool;

    /**
     * @var ServiceContest
     */
    private $serviceContest;

    function __construct(ServiceSchool $serviceSchool, SchoolFactory $factorySchool, ServiceContest $serviceContest) {
        $this->serviceSchool = $serviceSchool;
        $this->factorySchool = $factorySchool;
        $this->serviceContest = $serviceContest;
    }

    public function createContestant($options = 0, ControlGroup $group = null) {
        $container = new ModelContainer();
        $container->setCurrentGroup($group);

        if ($options & self::SHOW_CONTEST) {
            $container->addSelect('contest_id', _('Seminář'))
                    ->setItems($this->serviceContest->getTable()->fetchPairs('contest_id', 'name'))
                    ->setPrompt(_('Zvolit seminář'))
                    ->addRule(Form::FILLED, _('Je třeba zvolit seminář.'));
        }

        if ($options & self::REQUIRE_SCHOOL) {
            $school = $this->factorySchool->createSchoolSelect(SchoolFactory::SHOW_UNKNOWN_SCHOOL_HINT);
            $school->addRule(Form::FILLED, _('Je třeba zadat školu.'));
        } else {
            $school = $this->factorySchool->createSchoolSelect();
        }
        $container->addComponent($school, 'school_id');

        // TODO extract this element and made it more robust (show graduation year)
        $studyYear = $container->addSelect('study_year', _('Ročník'))
                ->setItems(array(
                    1 => '1. ročník SŠ',
                    2 => '2. ročník SŠ',
                    3 => '3. ročník SŠ',
                    4 => '4. ročník SŠ',
                    6 => '6. ročník ZŠ',
                    7 => '7. ročník ZŠ',
                    8 => '8. ročník ZŠ',
                    9 => '9. ročník ZŠ',
                ))->setOption('description', _('Kvůli zařazení do kategorie.'))
                ->setPrompt(_('Zvolit ročník'));

        if ($options & self::REQUIRE_STUDY_YEAR) {
            $studyYear->addRule(Form::FILLED, _('Je třeba zadat ročník.'));
        }


        $container->addText('class', _('Třída'))
                ->setOption('description', _('Kvůli případné školní korespondenci.'));

        return $container;
    }

}
