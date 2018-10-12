<?php

namespace SimpleParser\Loader;

use SimpleParser\Exceptions\{EmptyUrlException,
    RetrieveDataException,
    UncorrectedUrlException,
    UndefinedParsedUrlKeyException};

use SimpleParser\Parser;

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
     *
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
     * @return UrlLoader
     * @throws UncorrectedUrlException
     */
    public function setUrl(string $url, $context = null): self
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new UncorrectedUrlException(sprintf('Uncorrected url: %s', $url));
        }

        $this->url = $url;
        $this->context = $context;

        return $this;
    }

    /**
     * @return Parser
     * @throws EmptyUrlException
     * @throws RetrieveDataException
     */
    public function load(): Parser
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
        $document->loadHTML($content);

        return new Parser($document);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function prepareUrlQuery(string $url): string
    {
        return rawurlencode(rawurldecode($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function prepareUrl(string $url): string
    {
        $parsedUrl = parse_url($url);

        $urlParts = self::URL_PARTS;

        $preparedUrlTemplate = implode('', $this->prettyFastArrayIntersect($urlParts, array_flip($parsedUrl)));

        foreach ($parsedUrl as $key => &$value) {
            switch ($key) {
                case 'scheme':
                    {
                        $value .= '://';
                        break;
                    }
                case 'user':
                case 'path':
                    {
                        break;
                    }
                case 'pass':
                case 'port':
                    {
                        $value = ':' . $value;
                        break;
                    }
                case 'host':
                    {
                        if (isset($parsedUrl['user']) || isset($parsedUrl['pass'])) {
                            $value = '@' . $value;
                        }
                        break;
                    }
                case 'query':
                    {
                        $value = $this->prepareUrlQuery($value);
                        break;
                    }
                case 'fragment':
                    {
                        $value = '#' . $value;
                        break;
                    }
                default: break;
            }
        }
        unset($value);

        return strtr($preparedUrlTemplate, $parsedUrl);
    }

    private function prettyFastArrayIntersect(array $firstArray, array $secondArray): array
    {
        $count = \count($firstArray);
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            if (\in_array($firstArray[$i], $secondArray, true)) {
                $result[$i] = $firstArray[$i];
            }
        }

        return $result;
    }
}