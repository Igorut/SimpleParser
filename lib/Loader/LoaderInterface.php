<?php

namespace SimpleParser\Loader;

use SimpleParser\Document\Document;

interface LoaderInterface
{
    public const LIBXML_OPTIONS =
        LIBXML_COMPACT |
        LIBXML_BIGLINES |
        LIBXML_HTML_NOIMPLIED |
        LIBXML_HTML_NODEFDTD |
        LIBXML_PARSEHUGE |
        LIBXML_NOERROR;

    /**
     * Load document
     *
     * @return Document
     */
    public function load(): Document;
}