<?php

use PHPUnit\Framework\TestCase;
use eyupcanakman\SimpleHtmlDom\HtmlDomParser;

/**
 * Tests for SVG data URL preservation inside <style> tags.
 *
 *
 * @internal
 */
final class StyleSvgPreservationTest extends TestCase
{
    public function testUnquotedSvgUrlPreserved()
    {
        $html = '<style>.test{background:url(data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg"><rect fill="#fff" width="100%" height="100%"/></svg>)}</style><div class="test">Hello</div>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('</svg>', $style);
        static::assertStringContainsString('<rect', $style);
        static::assertStringContainsString('100%', $style);
    }

    public function testQuotedSvgUrlStillWorks()
    {
        $html = '<style>.icon{background:url("data:image/svg+xml;utf8,<svg viewBox=\"0 0 10 10\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"10\" height=\"10\"/></svg>")}</style><div>ok</div>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('</svg>', $style);
        static::assertStringContainsString('<svg', $style);
    }

    public function testMultipleSvgsInStylePreserved()
    {
        $html = '<style>.a{background:url(data:image/svg+xml,<svg><rect/></svg>)} .b{background:url(data:image/svg+xml,<svg><circle/></svg>)}</style><div>test</div>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('<rect', $style);
        static::assertStringContainsString('<circle', $style);
    }

    public function testStyleWithoutSvgUnchanged()
    {
        $html = '<style>.plain{color:red;font-size:14px}</style><div class="plain">text</div>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertSame('.plain{color:red;font-size:14px}', $style);
    }

    public function testBodyContentNotAffectedBySvgInStyle()
    {
        $html = '<style>.bg{background:url(data:image/svg+xml,<svg><path d="M0 0"/></svg>)}</style><div class="bg">Hello World</div>';
        $dom = new HtmlDomParser($html);

        static::assertSame('Hello World', $dom->findOne('.bg')->text());
    }

    public function testComplexSvgWithGroupsPreserved()
    {
        $html = '<style>.icon{background:url("data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\"><g><path d=\"M12 2C6.48 2 2 6.48 2 12\"/><circle cx=\"12\" cy=\"12\" r=\"5\"/></g></svg>")}</style><p>ok</p>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('<circle', $style);
        static::assertStringContainsString('</svg>', $style);
    }

    public function testSvgWithXlinkInStylePreserved()
    {
        $html = '<style>.icon{background:url("data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\"><use xlink:href=\"#icon\"/></svg>")}</style><p>text</p>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('xlink:href', $style);
    }

    public function testSvgInStyleWithCssCustomProperties()
    {
        $html = '<style>.a{--icon:url("data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\"><path d=\"M12 2z\"/></svg>")}</style><div class="a">check</div>';
        $dom = new HtmlDomParser($html);

        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('</svg>', $style);
        static::assertStringContainsString('--icon', $style);
    }

    public function testPercentEncodedSvgInStylePreserved()
    {
        $html = '<style>.icon::before{background:url("data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cpath/%3E%3C/svg%3E")}</style><div class="icon">text</div>';
        $dom = new HtmlDomParser($html);

        // Percent-encoded SVG doesn't contain < so won't trigger the protection,
        // but it shouldn't break anything either
        $style = $dom->findOne('style')->innerHtml();
        static::assertStringContainsString('url(', $style);
    }
}
