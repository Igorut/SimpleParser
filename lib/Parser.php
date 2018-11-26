<?php

namespace SimpleParser;

use SimpleParser\Document\Document;
use SimpleParser\Document\Element;
use SimpleParser\Exceptions\NodeIteratorException;
use SimpleParser\Exceptions\ParserException;
use SimpleParser\Helper\Iterator;

class Parser
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var Iterator
     */
    private $iterator;

    public function __construct(Document $document = null)
    {
        $this->document = $document;
        $this->iterator = new Iterator();
    }

    /**
     * Explode document content by delimiter
     *
     * @param string $delimiter
     *
     * @return array
     */
    public function explode(string $delimiter): array
    {
        $isPrettyOutput = $this->document->isPrettyOutput();

        if ($isPrettyOutput) {
            $this->document->setPrettyOutput(false);
        }

        $explodedContent = explode($delimiter, $this->document->getText());

        if ($isPrettyOutput) {
            $this->document->setPrettyOutput(true);
        }

        return $explodedContent;
    }

    /**
     * Find an element by id and return an instance of the Element with it
     *
     * @param string $id
     *
     * @return Element
     */
    public function getElementById(string $id): Element
    {
        return new Element($this->document->getDOMDocument()->getElementById($id));
    }

    /**
     * @param string $className
     *
     * @return Element[]
     *
     * @throws NodeIteratorException
     */
    public function getElementsByClassName(string $className): array
    {
        /** @var Element[] $matches */
        $matches = [];

        $this->iterator->iterateNodes($this->document->getDOMDocument(), function (\DOMNode $rootNode) use ($className, &$matches) {
            for ($i = 0; $i < $rootNode->childNodes->count(); $i++) {
                $node = $rootNode->childNodes->item($i);

                if ($node instanceof \DOMElement) {
                    $nodeClasses = explode(' ', $node->getAttribute('class'));

                    if (\in_array($className, $nodeClasses, true)) {
                        $matches[] = new Element($node);
                    }
                }

                yield $node;
            }

            yield;
        });

        return $matches;
    }

    /**
     * @param string $tag
     *
     * @throws NodeIteratorException
     */
    public function removeTag(string $tag): void
    {
        $this->removeTags([$tag]);
    }

    /**
     * Remove tags from document
     *
     * WARNING! To improve performance, if you want remove tags "head, link, meta", it's better to pass
     * an array with the following enumeration of tags
     * [
     *     'head',
     *     'link',
     *     'meta',
     *     and etc.
     * ]
     *
     * instead of this
     * [
     *     'link',
     *     'meta',
     *     'head',
     *     and etc.
     * ]
     *
     * With the second array, there will be more iterations over the document tags, when with first array we remove
     * tag 'head' with all 'meta' tags and some 'link' tags. So, if you want remove tags, which can be included in other
     * removing tags, better place parent tags earlier and child tags later
     *
     * @param array $tags
     *
     * @throws NodeIteratorException
     */
    public function removeTags(array $tags): void
    {
        $tagsToParseAgain = [];

        foreach ($tags as $tag) {
            $this->iterator->iterateNodes($this->document->getDOMDocument(), function (\DOMNode $rootNode) use ($tag) {
                for ($i = 0; $i < $rootNode->childNodes->count(); $i++) {
                    $node = $rootNode->childNodes->item($i);

                    if ($node->nodeName === $tag) {
                        $rootNode->removeChild($node);
                    } else {
                        yield $node;
                    }
                }

                yield;
            });

            if ($this->document->getDOMDocument()->getElementsByTagName($tag)->count() > 0) {
                $tagsToParseAgain[] = $tag;
            }
        }

        if (!empty($tagsToParseAgain)) {
            $this->removeTags($tagsToParseAgain);
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
            if (!isset($tagsCount[$tag])) {
                $tagsCount[$tag] = 0;
            }

            $tagsCount[$tag]++;
        }

        return $tagsCount;
    }

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

    /**
     * Return document instance
     *
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->iterator;
    }
}