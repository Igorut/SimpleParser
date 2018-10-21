<?php

namespace SimpleParser\Loader;

use SimpleParser\Document\Document;
use SimpleParser\Exceptions\{EmptyUrlException, RetrieveDataException, UncorrectedUrlException};

class UrlLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var resource
     */
    private $context;

    /**
     * The correct order parts of the url
     */
    private const URL_PARTS = [
        'scheme',
        'user',
        'pass',
        'host',
        'path',
        'query',
        'fragment'
    ];

    /**
     * @param string $url
     * @param null $context
     *
     * @throws UncorrectedUrlException
     *
     */
    public function __construct(string $url, $context = null)
    {
        $this->setUrl($url, $context);
    }

    /**
     * @param string $url
     * @param null $context
     *
     * @throws UncorrectedUrlException
     */
    public function setUrl(string $url, $context = null): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new UncorrectedUrlException(sprintf('Uncorrected url: %s', $url));
        }

        $this->url = $url;
        $this->context = $context;
    }

    /**
     * @inheritdoc
     *
     * @throws EmptyUrlException
     * @throws RetrieveDataException
     * @throws UncorrectedUrlException
     */
    public function load(): Document
    {
        if ($this->url === null) {
            throw new EmptyUrlException('Url is empty');
        }

        $preparedUrl = $this->prepareUrl($this->url);
        $content = file_get_contents($preparedUrl, false, $this->context);
        if ($content === false) {
            throw new RetrieveDataException(sprintf('Unable to retrieve data from: %s', $this->url));
        }

        $document = new \DOMDocument();
        $document->loadHTML($content, self::LIBXML_OPTIONS);

        return new Document($document);
    }

    /**
     * Prepare url before load
     *
     * Splits the url into component parts, forms a url template based on the received parts.
     * After, assembles the component parts into a url and converts the query part to RFC 3986
     *
     * @param string $url
     *
     * @return string
     * @throws UncorrectedUrlException
     */
    private function prepareUrl(string $url): string
    {
        if (false === $parsedUrl = parse_url($url)) {
            throw new UncorrectedUrlException(sprintf('Can\'t parse url: %s', $url));
        }

        foreach ($parsedUrl as $partKey => $value) {
            switch ($partKey) {
                case 'scheme':
                    {
                        $parsedUrl[$partKey] .= '://';
                        break;
                    }
                case 'pass':
                case 'port':
                    {
                        $parsedUrl[$partKey] = ':' . $value;
                        break;
                    }
                case 'host':
                    {
                        if (isset($parsedUrl['user']) || isset($parsedUrl['pass'])) {
                            $parsedUrl[$partKey] = '@' . $value;
                        }
                        break;
                    }
                case 'query':
                    {
                        $parsedUrl[$partKey] = rawurlencode(rawurldecode($value));
                        break;
                    }
                case 'fragment':
                    {
                        $parsedUrl[$partKey] = '#' . $value;
                        break;
                    }
                case 'user':
                case 'path':
                default:
                    break;
            }
        }

        $urlTemplate = implode('', array_intersect(self::URL_PARTS, array_keys($parsedUrl)));

        return strtr($urlTemplate, $parsedUrl);
    }
}