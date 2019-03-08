<?php

namespace FKSDB\Components\Grids\Fyziklani;

use FKSDB\Components\Grids\BaseGrid;
use Nette\Utils\Html;
use ServiceFyziklaniSubmit;

/**
 *
 * @author Michal Červeňák
 * @author Lukáš Timko
 */
abstract class SubmitsGrid extends BaseGrid {

    /**
     *
     * @var ServiceFyziklaniSubmit
     */
    protected $serviceFyziklaniSubmit;

    /**
     * FyziklaniSubmitsGrid constructor.
     * @param ServiceFyziklaniSubmit $serviceFyziklaniSubmit
     */
    public function __construct(ServiceFyziklaniSubmit $serviceFyziklaniSubmit) {
        $this->serviceFyziklaniSubmit = $serviceFyziklaniSubmit;
        parent::__construct();
    }

    /**
     * @throws \NiftyGrid\DuplicateColumnException
     */
    protected function addColumnTask() {
        $this->addColumn('label', _('Task'))->setRenderer(function ($row) {
            $model = \ModelFyziklaniSubmit::createFromTableRow($row);
            return $model->getTask()->label;
        });
    }

    /**
     * @throws \NiftyGrid\DuplicateColumnException
     */
    protected function addColumnState() {
        $this->addColumn('state', _('State'))->setRenderer(function ($row) {
            $model = \ModelFyziklaniSubmit::createFromTableRow($row);
            switch ($model->state) {
                case \ModelFyziklaniSubmit::STATE_CHECKED:
                    return Html::el('span')->addAttributes(['class' => 'badge badge-success'])->add(_('checked'));
                default:
                case \ModelFyziklaniSubmit::STATE_NOT_CHECKED:
                    return Html::el('span')->addAttributes(['class' => 'badge badge-danger'])->add(_('not checked'));
            }
        });
    }
}
