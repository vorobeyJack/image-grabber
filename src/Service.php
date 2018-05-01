<?php

namespace vrba\App;

/**
 * Class Service
 *
 * @package vrba\App
 */
class Service
{
    private $content;
    private $scheme;
    private $host;

    /**
     * Service constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->content = file_get_contents($url);
        $this->scheme = parse_url($url, PHP_URL_SCHEME);
        $this->host = parse_url($url, PHP_URL_HOST);
    }
}


