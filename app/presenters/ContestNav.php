<?php
/**
 * @author Michal Červeňák <miso@fykos.cz>
 * trait content contest, year, series and lang chooser
 */

use \FKSDB\Components\Controls;

/**
 * Trait ContestNav
 * @param $serviceContest ServiceContest
 */
trait ContestNav {

    /**
     * @var integer
     * @persistent
     */
    public $contestId;

    /**
     * @var integer
     * @persistent
     */
    public $year;

    /**
     * @var string
     * @persistent
     */
    public $lang;

    /**
     * @var integer
     * @persistent
     */
    public $series;
    /**
     * @var boolean
     */
    private $initialized = false;
    /**
     * @var object
     * @property integer contestId
     * @property integer year
     * @property integer series
     */
    private $newParams = null;

    protected function createComponentContestNav() {
        $control = new Controls\Navs\ContestNav($this->getYearCalculator(), $this->seriesCalculator, $this->session, $this->serviceContest, $this->getTranslator());
        $control->setRole($this->role);
        return $control;
    }

    /**
     * @return ModelContest
     */
    public function getSelectedContest() {
        $this->init();
        /**
         * @var ServiceContest $serviceContest
         */
        $serviceContest = $this->serviceContest;
        return $serviceContest->findByPrimary($this->contestId);
    }

    /**
     * @return int
     */
    public function getSelectedYear() {
        $this->init();
        return $this->year;
    }

    /**
     * @return int
     */
    public function getSelectedSeries() {
        $this->init();
        return $this->series;
    }

    public function getSelectedLanguage() {
        $this->init();
        return $this->lang;
    }

    /**
     * rewrite coreBasePresenter getLang
     * @return string
     */
    public function getLang() {
        return $this->getSelectedLanguage() ?: parent::getLang();
    }

    public function init() {
        if ($this->initialized) {
            return;
        }
        /**
         * @var $contestNav Controls\ContestNav\ContestNav
         */
        $contestNav = $this['contestNav'];
        $this->newParams = $contestNav->init((object)[
            'year' => $this->year,
            'contestId' => $this->contestId,
            'series' => $this->series,
            'lang' => $this->lang,
        ]);
    }

    /**
     * redirect to correct URL
     */
    protected function startupRedirects() {
        $this->init();
        if ($this->newParams ? ($this->newParams->year === -1) : (+$this->year === -1)) {
            //TODO lang
            $this->flashMessage('Pre túto rolu a seminár niesu dostupné žiadné ročniky');
        }
        if ($this->newParams ? ($this->newParams->series === -1) : (+$this->series === -1)) {
            //TODO lang
            $this->flashMessage('Pre tento ročník niesu dostupné žiadné série');
        }

        if (is_null($this->newParams)) {
            return;
        }
        $this->redirect('this', [
            'year' => $this->newParams->year ?: $this->year,
            'contestId' => $this->newParams->contestId ?: $this->contestId,
            'series' => $this->newParams->series ?: $this->series,
            'lang' => $this->newParams->lang ?: $this->lang,
        ]);
    }
}
