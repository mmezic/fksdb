<?php

namespace FKSDB\Components\Factories;

use FKSDB\Components\Grids\Schedule\GroupsGrid;
use FKSDB\Components\Grids\Schedule\ItemsGrid;
use FKSDB\Components\Grids\Schedule\PersonsGrid;
use FKSDB\ORM\ModelEvent;

/**
 * Class ScheduleFactory
 * @package FKSDB\Components\Factories
 */
class ScheduleFactory {
    /**
     * @param ModelEvent $event
     * @return GroupsGrid
     */
    public function createGroupsGrid(ModelEvent $event): GroupsGrid {
        return new GroupsGrid($event);
    }

    /**
     * @return ItemsGrid
     */
    public function createItemsGrid(): ItemsGrid {
        return new ItemsGrid();
    }
    /**
     * @return PersonsGrid
     */
    public function createPersonsGrid(): PersonsGrid {
        return new PersonsGrid();
    }

}
