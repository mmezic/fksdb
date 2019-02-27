<?php


namespace FKSDB\Components\Grids\Schedule;

use FKSDB\Components\Grids\BaseGrid;
use FKSDB\ORM\ModelEventOrg;
use FKSDB\ORM\ModelEventParticipant;
use FKSDB\ORM\Models\Schedule\ModelPersonSchedule;
use FKSDB\ORM\Models\Schedule\ModelScheduleItem;
use Nette\Utils\Html;
use NiftyGrid\DataSource\NDataSource;
use ORM\Models\Events\ModelFyziklaniTeam;

/**
 * Class PersonsGrid
 * @package FKSDB\Components\Grids\Schedule
 */
class PersonsGrid extends BaseGrid {
    /**
     * @var ModelScheduleItem
     */
    private $item;

    /**
     * ItemsGrid constructor.
     */
    public function __construct() {

        parent::__construct();
    }

    /**
     * @param ModelScheduleItem $item
     */
    public function setItem(ModelScheduleItem $item) {
        $this->item = $item;
        $persons = $this->item->getInterested();
        $dataSource = new NDataSource($persons);
        $this->setDataSource($dataSource);
    }

    /**
     * @param $presenter
     * @throws \NiftyGrid\DuplicateColumnException
     */
    protected function configure($presenter) {
        parent::configure($presenter);
        $this->paginate = false;

        $this->addColumn('person_schedule_id', _('#'));

        $this->addColumn('person', _('Person'))->setRenderer(function ($row) {
            $model = ModelPersonSchedule::createFromTableRow($row);
            return $model->getPerson()->getFullName();
        })->setSortable(false);

        $this->addColumnRole();

        $this->addColumn('state', _('State'))->setRenderer(function ($row) {
            $model = ModelPersonSchedule::createFromTableRow($row);
            return $model->state;
        });

    }
    /**
     * @throws \NiftyGrid\DuplicateColumnException
     */
    protected function addColumnRole() {
        $this->addColumn('role', _('Role'))
            ->setRenderer(function ($row) {
                $container = Html::el('span');
                $model = ModelPersonSchedule::createFromTableRow($row);
                $hasRole = false;
                $person = $model->getPerson();
                $eventId = $model->getScheduleItem()->getGroup()->event_id;

                $teachers = $person->getEventTeacher()->where('event_id', $eventId);
                foreach ($teachers as $row) {
                    $hasRole = true;
                    $team = ModelFyziklaniTeam::createFromTableRow($row);
                    $container->add(Html::el('span')
                        ->addAttributes(['class' => 'badge badge-9'])
                        ->add(_('Teacher') . ' - ' . $team->name));
                }

                $eventOrgs = $person->getEventOrg()->where('event_id', $eventId);
                foreach ($eventOrgs as $row) {
                    $hasRole = true;
                    $org = ModelEventOrg::createFromTableRow($row);
                    $container->add(Html::el('span')
                        ->addAttributes(['class' => 'badge badge-7'])
                        ->add(_('Org') . ' - ' . $org->note));
                }

                $eventParticipants = $person->getEventParticipant()->where('event_id', $eventId);
                foreach ($eventParticipants as $row) {
                    $hasRole = true;
                    $participant = ModelEventParticipant::createFromTableRow($row);
                    $container->add(Html::el('span')
                        ->addAttributes(['class' => 'badge badge-10'])
                        ->add(_('Participant') . ' - ' . _($participant->status)));
                }

                if (!$hasRole) {
                    $container->add(Html::el('span')
                        ->addAttributes(['class' => 'badge badge-danger'])
                        ->add(_('No role')));
                }
                return $container;
            })->setSortable(false);
    }
}
