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

    /**
     * Return \DOMDocument instance
     *
     * @return \DOMDocument
     */
    public function getDOMDocument(): \DOMDocument
    {
        return $this->document;
    }

    /**
     * Enable pretty output for text
     *
     * @return Document
     */
    public function enablePrettyOutput(): self
    {
        $this->document->formatOutput = true;

        return $this;
    }

    /**
     * Disable pretty output for text
     *
     * @return Document
     */
    public function disablePrettyOutput(): self
    {
        $this->document->formatOutput = false;

        return $this;
    }

    /**
     * Checks if the pretty output is enabled or disabled
     *
     * @return bool
     */
    public function isPrettyOutput(): bool
    {
        return $this->document->formatOutput;
    }

    /**
     * Return document content
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->document->saveHTML();
    }
}
