# Simple Html Dom Parser for PHP

A HTML DOM parser written in PHP - let you manipulate HTML in a very easy way!

A modern fork of [PHP Simple HTML DOM Parser](http://simplehtmldom.sourceforge.net/). Instead of string manipulation, it uses DOMDocument and modern PHP classes like Symfony CssSelector.

## What's different in this fork

- PHP 8.4 support (nullable parameter deprecations fixed)
- Symfony 8.0 support
- Leading combinator selectors (`> span`, `+ div`, `~ p`) now work in `find()`
- `@property` annotations on abstract classes for better static analysis (Psalm, PHPStan)
- Minimum PHP version corrected to 7.1.0 (matches actual nullable type usage)
- `findOneOrNull()` for PHP 8.0+ nullsafe operator support (`?->`)
- `remove()` method (jQuery-style alias for `delete()`)
- `text()` no longer includes `<style>` and `<script>` content
- Fixed crash when calling `delete()` on detached/root nodes
- HTML output no longer adds extra linebreaks not in the original
- Compound `text` / `comment` selectors: `find('div text')`, `find('p > text')`, `find('div comment')`
- SVG data URLs inside `<style>` tags no longer break parsing (unquoted `url()` fix)

## Features

- PHP 7.1+ & 8.x support
- Composer & PSR-4
- UTF-8 support
- Invalid HTML support (partly)
- Find tags on an HTML page with selectors just like jQuery
- Extract contents from HTML in a single line

## Install

```shell
composer require eyupcanakman/simple-html-dom
```

## Quick Start

```php
use eyupcanakman\SimpleHtmlDom\HtmlDomParser;

$dom = HtmlDomParser::str_get_html($str);
// or
$dom = HtmlDomParser::file_get_html($file);

$element = $dom->findOne('#css-selector');
$elements = $dom->findMulti('.css-selector');
$elementOrFalse = $dom->findOneOrFalse('#css-selector');
$elementOrNull = $dom->findOneOrNull('#css-selector');
$elementsOrFalse = $dom->findMultiOrFalse('.css-selector');

// PHP 8.0+ nullsafe operator
$text = $dom->findOneOrNull('.maybe-missing')?->text();

// Find text nodes inside an element
$textNodes = $dom->find('div text');        // all descendant text nodes
$directText = $dom->find('p > text');       // direct child text nodes only
$comments = $dom->find('div comment');      // comment nodes inside div
```

## Examples

[github.com/eyupcanakman/simple_html_dom/tree/master/example](https://github.com/eyupcanakman/simple_html_dom/tree/master/example)

## API

[github.com/eyupcanakman/simple_html_dom/tree/master/README_API.md](https://github.com/eyupcanakman/simple_html_dom/tree/master/README_API.md)

## License

MIT - see [LICENSE](LICENSE)

## Credits

Based on [PHP Simple HTML DOM Parser](http://simplehtmldom.sourceforge.net/).
