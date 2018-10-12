<?php

namespace SimpleParser\Loader;

use SimpleParser\Parser;

interface LoaderInterface
{
    public function load(): Parser;
}