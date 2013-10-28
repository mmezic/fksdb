<?php

namespace FKS\Config\Extensions;

use Nette\Config\CompilerExtension;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class NavigationExtension extends CompilerExtension {

    private $createdNodes = array();

    public function loadConfiguration() {
        parent::loadConfiguration();

        $builder = $this->getContainerBuilder();
        $config = $this->getConfig(array(
            'nodes' => array(),
            'structure' => array(),
        ));
        $navbar = $builder->addDefinition('navbar')
                ->setClass('FKS\Components\Controls\Navigation\NavBar');
        $navbar->setShared(true)->setAutowired(true);


        foreach ($config['nodes'] as $nodeId => $arguments) {
            $this->createNode($navbar, $nodeId, $arguments);
        }

        $this->createFromStructure($config['structure'], $navbar);


        $navbar->addSetup('$service->setStructure(?);', array($config['structure']));
    }

    private function createNode($navbar, $nodeId, $arguments = array()) {
        if (!isset($arguments['link'])) {
            $this->parseIdAsLink($nodeId, $arguments);
        }
        $this->createdNodes[$nodeId] = 1;
        $arguments['nodeId'] = $nodeId;
        $navbar->addSetup('$service->createNode(?, ?);', array($nodeId, $arguments));
    }

    private function createFromStructure($structure, $navbar, $parent = null) {
        foreach ($structure as $nodeId => $children) {
            if (is_array($children)) {
                if (!isset($this->createdNodes[$nodeId])) {
                    $this->createNode($navbar, $nodeId);
                    if ($parent) {
                        $navbar->addSetup('$service->addParent(?, ?);', array($nodeId, $parent));
                    }
                }
                $this->createFromStructure($children, $navbar, $nodeId);
            } else if (!is_array($children)) {
                $nodeId = $children;
                if (!isset($this->createdNodes[$nodeId])) {
                    $this->createNode($navbar, $nodeId);
                    if ($parent) {
                        $navbar->addSetup('$service->addParent(?, ?);', array($nodeId, $parent));
                    }
                }
            }
        }
    }

    private function parseIdAsLink($nodeId, &$arguments) {
        $FQAction = str_replace('.', ':', $nodeId);
        $a = strrpos($FQAction, ':');
        $presenterName = substr($FQAction, 0, $a);
        $action = substr($FQAction, $a + 1);
        $arguments['linkPresenter'] = $presenterName;
        $arguments['linkAction'] = $action;
        $arguments['linkParams'] = isset($arguments['params']) ? $arguments['params'] : null;
        unset($arguments['params']);
    }

}
