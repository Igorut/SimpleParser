<?php


namespace SimpleParser\Iterator;


interface IteratorInterface
{
    /**
     * @param \DOMNode $rootNode
     * @param callable $callback
     */
    public function iterateNodes(\DOMNode $rootNode, callable $callback);

    /**
     * @param \DOMNodeList $nodeList
     * @param callable $callback
     */
    public function iterateNodeList(\DOMNodeList $nodeList, callable $callback);
}