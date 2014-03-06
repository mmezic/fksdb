<?php

namespace FKSDB\Components\Events;

use Events\Machine\Machine;
use Events\Model\ApplicationHandler;
use Events\Model\ApplicationHandlerFactory;
use Events\Model\Grid\IHolderSource;
use Events\Model\Holder\Holder;
use FKS\Logging\FlashMessageDump;
use FKS\Logging\MemoryLogger;
use ModelEvent;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use Nette\Utils\Strings;
use SystemContainer;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class ApplicationsGrid extends Control {

    const NAME_PREFIX = 'application_';

    /**
     *
     * @var SystemContainer
     */
    private $container;

    /**
     * @var IHolderSource
     */
    private $source;

    /**
     * @var Holder[]
     */
    private $holders = array();

    /**
     * @var Machine[]
     */
    private $machines = array();

    /**
     * @var ModelEvent[]
     */
    private $eventApplications = array();

    /**
     * @var ApplicationHandler[]
     */
    private $handlers = array();

    /**
     * @var ApplicationHandlerFactory
     */
    private $handlerFactory;

    /**
     * @var FlashMessageDump
     */
    private $flashDump;

    /**
     * @var string
     */
    private $templateFile;

    function __construct(SystemContainer $container, IHolderSource $source, ApplicationHandlerFactory $handlerFactory, FlashMessageDump $flashDump) {
        parent::__construct();
        $this->container = $container;
        $this->source = $source;
        $this->handlerFactory = $handlerFactory;
        $this->flashDump = $flashDump;
        $this->processSource();
    }

    /**
     * @param string $template name of the standard template or whole path
     */
    public function setTemplate($template) {
        if (stripos($template, '.latte') !== false) {
            $this->templateFile = $template;
        } else {
            $this->templateFile = __DIR__ . DIRECTORY_SEPARATOR . "ApplicationsGrid.$template.latte";
        }
    }

    private function processSource() {
        $this->eventApplications = array();
        foreach ($this->source as $key => $holder) {
            $this->eventApplications[$key] = $holder->getEvent();
            $this->holders[$key] = $holder;
            $this->machines[$key] = $this->container->createEventMachine($holder->getEvent());
            $this->handlers[$key] = $this->handlerFactory->create($holder->getEvent(), new MemoryLogger()); //TODO it's a bit weird to create new logger for each handler
        }
    }

    protected function createComponent($name) {
        $key = null;
        if (Strings::startsWith($name, self::NAME_PREFIX)) {
            $key = substr($name, strlen(self::NAME_PREFIX));
        }
        if (!$key) {
            parent::createComponent($name);
        }


        $component = new ApplicationComponent($this->handlers[$key], $this->holders[$key], $this->flashDump);
        return $component;
    }

    protected function createTemplate($class = NULL) {
        $template = parent::createTemplate($class);
        $template->setTranslator($this->presenter->getTranslator());
        return $template;
    }

    public function render() {
        if (!$this->templateFile) {
            throw new InvalidStateException('Must set template for the grid.');
        }

        $this->template->eventApplications = $this->eventApplications;
        $this->template->holders = $this->holders;
        $this->template->machines = $this->machines;

        $this->template->setFile($this->templateFile);
        $this->template->render();
    }

}
