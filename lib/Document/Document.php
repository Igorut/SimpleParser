<?php

namespace SimpleParser\Document;

class Document
{
    /**
     * @var \DOMDocument
     */
    private $document;

    public function __construct(\DOMDocument $document)
    {
        $this->document = $document;
    }

    public function getDocument(): \DOMDocument
    {
        return $this->document;
    }

    public function enablePrettyOutput(): self
    {
        $this->document->formatOutput = true;

        return $this;
    }

    public function disablePrettyOutput(): self
    {
        $this->document->formatOutput = false;

        return $this;
    }

    public function isPrettyOutput(): bool
    {
        return $this->document->formatOutput;
    }

    public function getText(): string
    {
        return $this->document->saveHTML();
    }
}