<?php

namespace vrba\App;

use vrba\App\Exception\InvalidImageException;

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
    private $directoryPath;

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

    /**
     * Creates images directory.
     *
     * @return Service
     */
    private function makeImagesDirectory(): Service
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'img';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->directoryPath = $path;

        return $this;
    }

    /**
     * @throws InvalidImageException
     */
    private function parse()
    {
        foreach ($this->content->find('img') as $img) {
            if (!$this->isImageCorrect($img)) {
                throw new InvalidImageException();
            }

            //@todo

        }
    }

    /**
     * Checks if image is correct.
     *
     * @param string $src
     * @return bool
     */
    private function isImageCorrect(string $src): bool
    {
        $imageInfo = getimagesize($src);
        $imageType = $imageInfo[2];

        return !empty($src) && in_array($imageType, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]);
    }
}
