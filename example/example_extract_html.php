<?php

require_once '../vendor/autoload.php';

echo eyupcanakman\SimpleHtmlDom\HtmlDomParser::file_get_html('https://www.google.com/')->plaintext;
