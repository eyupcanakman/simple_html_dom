<?php

use eyupcanakman\SimpleHtmlDom\SimpleHtmlDomInterface;
use eyupcanakman\SimpleHtmlDom\SimpleHtmlDomNode;
use eyupcanakman\SimpleHtmlDom\SimpleHtmlDomNodeInterface;

require_once '../vendor/autoload.php';

/**
 * @param \eyupcanakman\SimpleHtmlDom\HtmlDomParser $dom
 * @param string                     $selector
 * @param string                     $keyword
 *
 * @return SimpleHtmlDomInterface[]|SimpleHtmlDomNodeInterface<SimpleHtmlDomInterface>
 */
function find_contains(
    \eyupcanakman\SimpleHtmlDom\HtmlDomParser $dom,
    string $selector,
    string $keyword
) {
    // init
    $elements = new SimpleHtmlDomNode();

    foreach ($dom->find($selector) as $e) {
        if (strpos($e->innerText(), $keyword) !== false) {
            $elements[] = $e;
        }
    }

    return $elements;
}

// -----------------------------------------------------------------------------

$html = '
<p class="lall">lall<br></p>
<p class="lall">foo</p>
<ul><li class="lall">test321<br>foo</li><!----></ul>
';

$document = new \eyupcanakman\SimpleHtmlDom\HtmlDomParser($html);

foreach (find_contains($document, '.lall', 'foo') as $child_dom) {
    echo $child_dom->html() . "\n";
}
