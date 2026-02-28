<?php

declare(strict_types=1);

namespace eyupcanakman\SimpleHtmlDom;

use Symfony\Component\CssSelector\CssSelectorConverter;

class SelectorConverter
{
    /**
     * @var string[]
     *
     * @phpstan-var array<string,string>
     */
    protected static $compiled = [];

    /**
     * @param string $selector
     * @param bool $ignoreCssSelectorErrors
     *                                      <p>
     *                                      Ignore css selector errors and use the $selector as it is on error,
     *                                      so that you can also use xPath selectors.
     *                                      </p>
     * @param bool $isForHtml
     *
     * @return string
     */
    public static function toXPath(string $selector, bool $ignoreCssSelectorErrors = false, bool $isForHtml = true)
    {
        if (isset(self::$compiled[$selector])) {
            return self::$compiled[$selector];
        }

        // Select DOMText
        if ($selector === 'text') {
            return '//text()';
        }

        // Select DOMComment
        if ($selector === 'comment') {
            return '//comment()';
        }

        if (\strpos($selector, '//') === 0) {
            return $selector;
        }

        // Handle selectors starting with a combinator (> child, + adjacent, ~ sibling)
        $trimmedSelector = \ltrim($selector);
        if (isset($trimmedSelector[0]) && \in_array($trimmedSelector[0], ['>', '+', '~'], true)) {
            $combinator = $trimmedSelector[0];
            $restSelector = \ltrim(\substr($trimmedSelector, 1));

            if ($restSelector === '') {
                throw new \RuntimeException('Selector cannot end with a combinator: ' . $selector);
            }

            $restXPath = self::toXPath($restSelector, $ignoreCssSelectorErrors, $isForHtml);

            switch ($combinator) {
                case '>':
                    $xPathQuery = \str_replace('descendant-or-self::', '/*/', $restXPath);
                    break;
                case '+':
                    $xPathQuery = \str_replace('descendant-or-self::', '/*/following-sibling::*[1]/self::', $restXPath);
                    break;
                case '~':
                    $xPathQuery = \str_replace('descendant-or-self::', '/*/following-sibling::', $restXPath);
                    break;
                default:
                    throw new \RuntimeException('Unexpected combinator: ' . $combinator);
            }

            self::$compiled[$selector] = $xPathQuery;

            return $xPathQuery;
        }

        if (!\class_exists(CssSelectorConverter::class)) {
            throw new \RuntimeException('Unable to filter with a CSS selector as the Symfony CssSelector 2.8+ is not installed (you can use filterXPath instead).');
        }

        $converterKey = '-' . $isForHtml . '-' . $ignoreCssSelectorErrors . '-';
        static $converterArray = [];
        if (!isset($converterArray[$converterKey])) {
            $converterArray[$converterKey] = new CssSelectorConverter($isForHtml);
        }
        $converter = $converterArray[$converterKey];
        assert($converter instanceof CssSelectorConverter);

        if ($ignoreCssSelectorErrors) {
            try {
                $xPathQuery = $converter->toXPath($selector);
            } catch (\Exception $e) {
                $xPathQuery = $selector;
            }
        } else {
            $xPathQuery = $converter->toXPath($selector);
        }

        self::$compiled[$selector] = $xPathQuery;

        return $xPathQuery;
    }
}
