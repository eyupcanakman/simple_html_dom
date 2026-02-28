<?php

use PHPUnit\Framework\TestCase;
use eyupcanakman\SimpleHtmlDom\HtmlDomParser;
use eyupcanakman\SimpleHtmlDom\SelectorConverter;

/**
 * Tests for compound selectors ending with text/comment pseudo-selectors.
 *
 *
 * @internal
 */
final class SelectorConverterTextCommentTest extends TestCase
{
    protected function setUp(): void
    {
        $ref = new \ReflectionProperty(SelectorConverter::class, 'compiled');
        $ref->setValue(null, []);
    }

    // --- Unit tests: XPath generation ---

    public function testTextAloneStillWorks()
    {
        static::assertSame('//text()', SelectorConverter::toXPath('text'));
    }

    public function testCommentAloneStillWorks()
    {
        static::assertSame('//comment()', SelectorConverter::toXPath('comment'));
    }

    public function testDivTextProducesDescendantTextXPath()
    {
        $xpath = SelectorConverter::toXPath('div text');
        static::assertStringContainsString('div', $xpath);
        static::assertStringEndsWith('//text()', $xpath);
    }

    public function testDivDirectTextProducesChildTextXPath()
    {
        $xpath = SelectorConverter::toXPath('div > text');
        static::assertStringContainsString('div', $xpath);
        static::assertStringEndsWith('/text()', $xpath);
        // Should NOT contain //text() — only /text()
        static::assertStringNotContainsString('//text()', $xpath);
    }

    public function testDivCommentProducesDescendantCommentXPath()
    {
        $xpath = SelectorConverter::toXPath('div comment');
        static::assertStringContainsString('div', $xpath);
        static::assertStringEndsWith('//comment()', $xpath);
    }

    public function testClassSelectorWithText()
    {
        $xpath = SelectorConverter::toXPath('.content text');
        static::assertStringEndsWith('//text()', $xpath);
    }

    public function testIdSelectorWithText()
    {
        $xpath = SelectorConverter::toXPath('#main text');
        static::assertStringEndsWith('//text()', $xpath);
    }

    public function testCompoundCssSelectorWithText()
    {
        $xpath = SelectorConverter::toXPath('div.wrapper > p text');
        static::assertStringEndsWith('//text()', $xpath);
    }

    // --- Integration tests: HtmlDomParser::find() ---

    public function testFindDivTextReturnsTextNodes()
    {
        $html = '<div><p>Hello <strong>World</strong></p><span>Foo</span></div>';
        $dom = new HtmlDomParser($html);

        $texts = $dom->find('div text');
        static::assertCount(3, $texts);

        $values = [];
        foreach ($texts as $t) {
            $values[] = $t->text();
        }
        // text() trims whitespace, so "Hello " becomes "Hello"
        static::assertSame(['Hello', 'World', 'Foo'], $values);
    }

    public function testFindPTextReturnsOnlyPDescendantTextNodes()
    {
        $html = '<div><p>Hello <strong>World</strong></p><span>Foo</span></div>';
        $dom = new HtmlDomParser($html);

        $texts = $dom->find('p text');
        static::assertCount(2, $texts);
    }

    public function testFindStrongTextReturnsSingleNode()
    {
        $html = '<div><p>Hello <strong>World</strong></p></div>';
        $dom = new HtmlDomParser($html);

        $texts = $dom->find('strong text');
        static::assertCount(1, $texts);
        static::assertSame('World', $texts[0]->text());
    }

    public function testFindDirectChildText()
    {
        $html = '<p>Direct <strong>Nested</strong></p>';
        $dom = new HtmlDomParser($html);

        // "p > text" should only return the direct text child "Direct", not "Nested"
        $texts = $dom->find('p > text');
        static::assertCount(1, $texts);
        static::assertSame('Direct', $texts[0]->text());
    }

    public function testFindDivCommentReturnsCommentNodes()
    {
        $html = '<div><!-- inside div --><p>Hello<!-- inside p --></p></div>';
        $dom = new HtmlDomParser($html);

        $comments = $dom->find('div comment');
        static::assertCount(2, $comments);
    }

    public function testFindPCommentReturnsOnlyPComments()
    {
        $html = '<div><!-- div comment --><p><!-- p comment --></p></div>';
        $dom = new HtmlDomParser($html);

        $comments = $dom->find('p comment');
        static::assertCount(1, $comments);
    }

    public function testCachingWorksForCompoundTextSelector()
    {
        // Call twice to test the cache path
        $xpath1 = SelectorConverter::toXPath('div text');
        $xpath2 = SelectorConverter::toXPath('div text');
        static::assertSame($xpath1, $xpath2);
    }
}
