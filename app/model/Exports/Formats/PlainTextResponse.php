<?php

namespace Exports\Formats;

use Nette\Application\IResponse;
use Nette\Object;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class PlainTextResponse extends Object implements IResponse {

    private $content;
    private $name;

    /**
     * PlainTextResponse constructor.
     * @param $content
     * @param null $name
     */
    function __construct($content, $name = null) {
        $this->content = $content;
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param \Nette\Http\IRequest $httpRequest
     * @param \Nette\Http\IResponse $httpResponse
     */
    public function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse) {
        $httpResponse->setContentType('text/plain', 'utf-8');

        if ($this->name) {
            $httpResponse->setHeader('Content-Disposition', 'attachment; filename="' . $this->name . '"');
        } else {
            $httpResponse->setHeader('Content-Disposition', 'attachment');
        }

        $httpResponse->setHeader('Content-Length', strlen($this->content));
        echo $this->content;
    }

}
