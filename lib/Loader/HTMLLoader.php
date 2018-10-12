<?php

namespace SimpleParser\Loader;

use SimpleParser\Parser;

class HTMLLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $html;

    public function setHTML(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function load(): Parser
    {
        $document = new \DOMDocument();
        $document->loadHTML($this->html);

        return new Parser($document);
    }
}