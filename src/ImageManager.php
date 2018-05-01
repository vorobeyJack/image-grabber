<?php

namespace vrba\App;

use vrba\App\Exception\IncorrectUrlException;
use vrba\App\Exception\InvalidImageException;
use vrba\App\Factory\ImageFileFactory;

/**
 * Class ImageManager
 *
 * @package vrba\App
 */
class ImageManager
{
    /**
     * Parsed page content.
     *
     * @var string
     */
    private $pageContent;

    /**
     * Image directory path.
     *
     * @var string
     */
    private $directoryPath;

    /**
     * ImageManager constructor.
     *
     * @param string $url
     * @throws IncorrectUrlException
     */
    public function __construct(string $url)
    {
        if(!$this->isUrlCorrect($url)) {
            throw new IncorrectUrlException();
        }

        $this->pageContent = file_get_contents($url);
        $this->directoryPath = $this->makeImagesDirectory();
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

        ImageFileFactory::createFromArray([
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
    public function parse()
    {
        foreach ($this->pageContent->find('img') as $img) {
            if (!$this->isImageCorrect($img)) {
                throw new InvalidImageException();
            }

            $this->saveImageToLocalStorage($img->src);
        }
    }

    /**
     * Checks url for correctly value.
     *
     * @param string $url
     * @return bool
     */
    private function isUrlCorrect(string $url) : bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
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
