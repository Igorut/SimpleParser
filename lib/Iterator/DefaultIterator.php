<?php

namespace SimpleParser\Iterator;

use SimpleParser\Exceptions\NodeIteratorException;

class DefaultIterator implements IteratorInterface
{
    public const INTERRUPTED = 8;
    public const FORCE_STOP = 16;
    private const CONTINUE = 32;

    /**
     * @inheritDoc
     *
     * Iterate over nodes from root node
     *
     * Callback function SHOULD yield \DOMNode or signal
     *
     * 1. INTERRUPTED: to stop iterating current node tree passed from $rootNode. Example:
     * We passed <div class="first">
     *              <div class="second">
     *                  <span></span>
     *                  <a></a>
     *              </div>
     *              <div class="third">
     *                  <div>
     *                      <span></span>
     *                  </div>
     *              </div>
     *           </div>
     * And now we are starting iterating all child nodes inside div.first, div.second at first, if now callback
     * will yield INTERRUPTED signal we will stop iterating over children of div.first
     *
     * 2. FORCE_STOP: to stop iterating completely
     *
     * @return int
     *
     * @throws NodeIteratorException    if the callback returns something else than the \Generator instance
     *                                  or was yielded unknown signal
     */
    public function iterateNodes(\DOMNode $rootNode, callable $callback): int
    {
        if ($rootNode->hasChildNodes()) {
            /** @var \Generator $generator */
            $generator = $callback($rootNode);

            if (!($generator instanceof \Generator)) {
                throw new NodeIteratorException(
                    sprintf('Callback should return a instance of Generator, %s returned', \gettype($generator))
                );
            }

            while (true) {
                $response = $generator->current();

                if (!($response instanceof \DOMNode)) {
                    switch ($response) {
                        case null:
                        case self::INTERRUPTED:
                            break 2;
                        case self::FORCE_STOP:
                            return $response;
                        default:
                            throw new NodeIteratorException('Unknown signal yielded from callback function!');
                    }
                }

                if ($this->iterateNodes($response, $callback) === self::FORCE_STOP) {
                    return self::FORCE_STOP;
                }

                $generator->next();
            }
        }

        return self::CONTINUE;
    }

    /**
     * @inheritDoc
     *
     * Iterate over node list and apply callback for each element
     *
     * If callback return false, iterating will end
     */
    public function iterateNodeList(\DOMNodeList $nodeList, callable $callback): void
    {
        $nodeCount = $nodeList->count();

        for ($i = 0; $i < $nodeCount; $i++) {
            if ($callback($nodeList->item($i)) === false) {
                break;
            }
        }
    }
}
