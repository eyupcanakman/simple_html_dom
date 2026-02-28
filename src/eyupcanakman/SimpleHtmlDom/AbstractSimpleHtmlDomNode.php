<?php

declare(strict_types=1);

namespace eyupcanakman\SimpleHtmlDom;

/**
 * {@inheritdoc}
 *
 * @property-read int      $length
 *                                    <p>The list items count.</p>
 * @property-read string[] $outertext
 *                                    <p>Get dom node's outer html.</p>
 * @property-read string[] $plaintext
 *                                    <p>Get dom node's plain text.</p>
 */
abstract class AbstractSimpleHtmlDomNode extends \ArrayObject
{
    /** @noinspection MagicMethodsValidityInspection */

    /**
     * @param string $name
     *
     * @return array|int|null
     */
    public function __get($name)
    {
        // init
        $name = \strtolower($name);

        if ($name === 'length') {
            return $this->count();
        }

        if ($this->count() > 0) {
            $return = [];

            foreach ($this as $node) {
                if ($node instanceof SimpleHtmlDomInterface) {
                    $return[] = $node->{$name};
                }
            }

            return $return;
        }

        if ($name === 'plaintext' || $name === 'outertext') {
            return [];
        }

        return null;
    }

    /**
     * @param string   $selector
     * @param int|null $idx
     *
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>|SimpleHtmlDomNodeInterface[]|null
     */
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // init
        $html = '';

        foreach ($this as $node) {
            $html .= $node->outertext;
        }

        return $html;
    }

    /**
     * @param string $selector
     * @param int|null   $idx
     *
     * @return SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>|SimpleHtmlDomNodeInterface[]|null
     */
    abstract public function find(string $selector, $idx = null);
}
