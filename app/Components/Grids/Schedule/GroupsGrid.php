<?php

namespace FKSDB\Components\Grids\Schedule;

use FKSDB\Components\Grids\BaseGrid;
use FKSDB\ORM\ModelEvent;
use NiftyGrid\DataSource\NDataSource;

/**
 * Class GroupsGrid
 * @package FKSDB\Components\Grids\Schedule
 */
class GroupsGrid extends BaseGrid {
    /**
     * @var ModelEvent
     */
    private $event;

    /**
     * GroupsGrid constructor.
     * @param ModelEvent $event
     */
    public function __construct(ModelEvent $event) {
        parent::__construct();
        $this->event = $event;
    }

    /**
     * @param $presenter
     * @throws \NiftyGrid\DuplicateButtonException
     * @throws \NiftyGrid\DuplicateColumnException
     */
    protected function configure($presenter) {
        parent::configure($presenter);
        $this->paginate = false;
        $groups = $this->event->getScheduleGroups();

        $dataSource = new NDataSource($groups);
        $this->setDataSource($dataSource);
        $this->addColumn('schedule_group_id', _('#'));

        $this->addColumn('schedule_group_type', _('Type'))->setRenderer(function ($row) {
            // TODO
            return $row->schedule_group_type;
        });
        $this->addColumn('start', _('Start'))->setRenderer(function ($row) {
            return $row->start->format('Y-m-d');
        });
        $this->addColumn('end', _('End'))->setRenderer(function ($row) {
            return $row->end->format('Y-m-d');
        });

        $this->addButton('detail', _('Detail'))->setText(_('Detail'))
            ->setLink(function ($row) {
                return $this->getPresenter()->link('group', ['id' => $row->schedule_group_id]);
            });

        /* $this->addButton('delete', _('Remove'))->setClass('btn btn-sm btn-danger')->setText(_('Remove'))
             ->setLink(function ($row) {
                 return $this->link('delete!', $row->event_accommodation_id);
             })->setConfirmationDialog(function () {
                 return _('Opravdu smazat ubytovaní?');
             });

         $this->addGlobalButton('add')
             ->setLabel(_('Add accommodation'))
             ->setLink($this->getPresenter()->link('create'));
        */
    }
}
