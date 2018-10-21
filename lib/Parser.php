<?php

namespace SimpleParser;

use SimpleParser\Document\Document;
use SimpleParser\Exceptions\ParserException;

class Parser
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @param Document $document
     *
     * @return Parser
     */
    public function setDocument(Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function explode(string $delimiter): array
    {
        return \explode($delimiter, $this->document->disablePrettyOutput()->getText());
    }

    public function explodeByTag(string $tag)
    {
        //todo
    }

    public function findElementById(string $id)
    {
        //todo
    }

    public function findElementsByClassName(string $className)
    {
        //todo
    }

    public function removeTag(string $tag): void
    {
        $this->removeTags([$tag]);
    }

    public function removeTags(array $tags): void
    {
        $tagsToParseAgain = [];

        foreach ($tags as $tag) {
            $this->parse($this->document->getDocument(), $tag);

            if ($this->document->getDocument()->getElementsByTagName($tag)->length > 0) {
                $tagsToParseAgain[] = $tag;
            }
        }

        if (\count($tagsToParseAgain) > 0) {
            $this->removeTags($tagsToParseAgain);
        }
    }

    private function parse(\DOMNode $parent, string $tag): void
    {
        if ($parent->hasChildNodes()) {
            for ($i = 0; $i < $parent->childNodes->length; $i++) {
                $item = $parent->childNodes->item($i);
                if ($item->nodeName === $tag) {
                    $parent->removeChild($item);
                } else {
                    $this->parse($item, $tag);
                }
            }
        }
    }

    /**
     * Return count of all tags in document
     *
     * output:
     * [
     *     'html' => 1,
     *     'head' => 1,
     *     'link' => 10,
     *     'body' => 1,
     *     'div' => 26
     *     and etc.
     * ]
     *
     * @return array
     * @throws ParserException
     */
    public function getTagCount(): array
    {
        if (preg_match_all('/<([a-z]+)[\s|>]/ui', $this->document->getText(), $tags, PREG_PATTERN_ORDER) === false) {
            throw new ParserException(
                sprintf('Error finding tags with regular expression in method: %s', __METHOD__)
            );
        }

        $tagsCount = [];
        foreach ($tags[1] as $tag) {
            if (isset($tagsCount[$tag])) {
                $tagsCount[$tag]++;
            } else {
                $tagsCount[$tag] = 1;
            }
        }

        return $tagsCount;
    }
}