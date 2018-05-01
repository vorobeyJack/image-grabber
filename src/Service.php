<?php

namespace vrba\App;

use vrba\App\Exception\InvalidImageException;
use vrba\App\Factory\ImageFactory;

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

    private $imageFactory;

    /**
     * Service constructor.
     *
     * @param string $url
     */
    public function __construct(string $url, ImageFactory $imageFactory)
    {
        $this->content = file_get_contents($url);
        $this->scheme = parse_url($url, PHP_URL_SCHEME);
        $this->host = parse_url($url, PHP_URL_HOST);
        $this->directoryPath = $this->makeImagesDirectory();

        $this->imageFactory = $imageFactory;
    }

    /**
     * Saves images into local storage.
     *
     * @param string $filePath
     */
    private function saveImageToLocalStorage(string $filePath)
    {
        $imageName = $this->generateImageName($filePath);
        $imageType = exif_imagetype($filePath);
        $imagePath = $filePath . DIRECTORY_SEPARATOR . $imageName;

        $this->imageFactory->createFromArray([
            'path' => $imagePath,
            'type' => $imageType,
            'name' => $imageName,
        ]);
    }

    /**
     * Creates images directory.
     *
     * @return string
     */
    private function makeImagesDirectory(): string
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'img';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
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

            $this->saveImageToLocalStorage($img->src);
        }
    }

    /**
     * Generates image name.
     *
     * @param string $src
     * @return string
     */
    private function generateImageName(string $src): string
    {
        return time() . '_' . pathinfo($src, PATHINFO_EXTENSION);
    }

    /**
     * Checks if image is correct.
     *
     * @param string $src
     * @return bool
     */
    private function isImageCorrect(string $src): bool
    {
        $imageType = exif_imagetype($src);

        return !empty($src) && in_array($imageType, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]);
    }
}
