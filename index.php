<?php

use SimpleParser\Loader\UrlLoader;
use SimpleParser\Parser;

include 'vendor/autoload.php';

$start = microtime(true);
$url = 'https://yandex.ru/';
$parser = new Parser();

try {
    $loader = new UrlLoader($url);
    $document = $loader->load();
    $parser->setDocument($document);

//    $parser->removeTags(['link', 'meta', 'style', 'script', 'noscript', 'head']);

    dump($parser->getDocument()->enablePrettyOutput()->getText());
} catch (\SimpleParser\Exceptions\ParserException $exception) {
    echo $exception->getMessage();
}

echo microtime(true) - $start;
