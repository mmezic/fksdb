<?php

/*
 * For presenters that provide series no. context.
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */

/**
 * Interface ISeriesPresenter
 */
interface ISeriesPresenter extends IContestPresenter {

    /** @return int */
    public function getSelectedSeries();
}

