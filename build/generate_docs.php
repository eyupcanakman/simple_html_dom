<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

$readmeText = (new \voku\PhpReadmeHelper\GenerateApi())->generate(
    __DIR__ . '/../src/',
    __DIR__ . '/docs/api.md',
    [
        \eyupcanakman\SimpleHtmlDom\DomParserInterface::class,
        \eyupcanakman\SimpleHtmlDom\SimpleHtmlDomNodeInterface::class,
        \eyupcanakman\SimpleHtmlDom\SimpleHtmlDomInterface::class
    ]
);

file_put_contents(__DIR__ . '/../README_API.md', $readmeText);
