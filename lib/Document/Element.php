<?php

namespace SimpleParser\Document;

use SimpleParser\Exceptions\SetAttributeException;

class Element
{
    /**
     * @var \DOMElement
     */
    private $element;

    public function __construct(\DOMElement $element)
    {
        $this->element = $element;
    }

    /**
     * Return all attributes for element
     *
     * @return \DOMNamedNodeMap
     */
    public function getAttributes(): \DOMNamedNodeMap
    {
        return $this->element->attributes;
    }

    /**
     * Return attribute value
     *
     * @param string $name
     *
     * @return mixed return attribute value or empty string if attribute not exist
     */
    public function getAttribute(string $name)
    {
        return $this->element->getAttribute($name);
    }

    /**
     * Adds a new attribute or updates existing one
     *
     * @param string $name
     * @param $value
     *
     * @return self
     *
     * @throws SetAttributeException if passed value is bool or isn't scalar or on setting failure
     */
    public function setAttribute(string $name, $value): self
    {
        if (\is_bool($value) || !\is_scalar($value)) {
            throw new SetAttributeException(sprintf('Values can only be scalar, %s given', \gettype($value)));
        }

        $result = $this->element->setAttribute($name, $value);

        if ($result === false) {
            throw new SetAttributeException(\sprintf('Failed to set attribute %s with value %s', $name, $value));
        }

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param string $name
     *
     * @return bool true on success or false on failure
     */
    public function removeAttribute(string $name): bool
    {
        return $this->element->removeAttribute($name);
    }

    /**
     * Checks to see if attribute exists
     *
     * @param string $name
     *
     * @return bool true on success or false on failure
     */
    public function hasAttribute(string $name): bool
    {
        return $this->element->hasAttribute($name);
    }

    /**
     * Checks if the element has a passed class
     *
     * @param string $class
     *
     * @return bool true on success or false on failure
     */
    public function hasClass(string $class): bool
    {
        $classList = explode(' ', $this->getAttribute('class'));

        foreach ($classList as $className) {
            if ($className === $class) {
                return true;
            }
        }

        return false;
    }
}
