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
     * Set pretty output for document content
     *
     * @param bool $value
     *
     * @return Document
     */
    public function setPrettyOutput(bool $value): self
    {
        $this->document->formatOutput = $value;

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
