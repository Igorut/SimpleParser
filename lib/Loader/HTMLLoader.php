<?php

namespace SimpleParser\Loader;

use SimpleParser\Document\Document;

class HTMLLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $html;

    /**
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->html = $html;
    }

    /**
     * @param string $html
     */
    public function setHTML(string $html): void
    {
        $this->html = $html;
    }

    /**
     * @inheritdoc
     */
    public function load(): Document
    {
        $document = new \DOMDocument();
        $document->loadHTML($this->html, self::LIBXML_OPTIONS);

        return new Document($document);
    }
}
