<?php

namespace ORM\Models\Events;

use AbstractModelSingle;
use DbNames;
use Nette\Database\Table\Selection;
use Nette\DateTime;

/**
 * @property string category
 * @property string name
 * @property integer e_fyziklani_team_id
 * @property integer event_id
 * @property integer points
 * @property string status
 * @property DateTime created
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @author Michal Červeňák <miso@fykos.cz>
 *
 */
class ModelFyziklaniTeam extends AbstractModelSingle {

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->name;
    }

    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getSubmits(): Selection {
        return $this->related(DbNames::TAB_FYZIKLANI_SUBMIT, 'e_fyziklani_team_id')->where('points IS NOT NULL');
    }

    /**
     * @return null|\ModelFyziklaniTeamPosition
     */
    public function getPosition() {
        $row = $this->related(DbNames::TAB_FYZIKLANI_TEAM_POSITION, 'e_fyziklani_team_id')->fetch();
        if ($row) {
            return \ModelFyziklaniTeamPosition::createFromTableRow($row);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasOpenSubmitting(): bool {
        $points = $this->points;
        return !is_numeric($points);
    }

    /**
     * @param bool $includePosition
     * @return array
     */
    public function __toArray(bool $includePosition = false): array {
        $data = [
            'created' => $this->created->format('c'),
            'category' => $this->category,
            'name' => $this->name,
            'status' => $this->status,
            'teamId' => $this->e_fyziklani_team_id,
        ];
        $position = $this->getPosition();
        if ($includePosition && $position) {
            $data['x'] = $position->col;
            $data['y'] = $position->row;
            $data['roomId'] = $position->getRoom()->room_id;
        }
        return $data;
    }

}
