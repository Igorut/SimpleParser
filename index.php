<?php

use SimpleParser\Loader\UrlLoader;

include 'vendor/autoload.php';

$start = microtime(true);

$url = 'https://yandex.ru/';
$urlLoader = new UrlLoader();

try {
    $urlLoader->setUrl($url);

    $parser = $urlLoader->load();
    $parser->setPrettyFormatOutput(true);

//    dump($parser->getText());
    dump($parser->explode("\n"));
} catch (\SimpleParser\Exceptions\UncorrectedUrlException $exception) {
    echo $exception->getMessage();
} catch (\SimpleParser\Exceptions\RetrieveDataException $exception) {
    echo $exception->getMessage();
} catch (\SimpleParser\Exceptions\EmptyUrlException $exception) {
    echo $exception->getMessage();
}
