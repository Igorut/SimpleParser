<?php

namespace SimpleParser;

class Parser
{
    /**
     * @var \DOMDocument
     */
    private $document;

    public function __construct(\DOMDocument $document)
    {
        $this->document = $document;
    }

    public function getText(): string
    {
        return $this->document->saveHTML();
    }

    public function explode(string $delimiter): array
    {
        return \explode($delimiter, $this->document->saveHTML());
    }

    public function setPrettyFormatOutput(bool $value): self
    {
        $this->document->formatOutput = $value;

        return $this;
    }
}