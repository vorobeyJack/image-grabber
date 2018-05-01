<?php

namespace vrba\App;

use vrba\App\Exception\IncorrectUrlException;
use vrba\App\Factory\ImageFileFactory;

/**
 * Class ImageManager
 *
 * @package vrba\App
 */
class ImageManager
{
    /**
     * DOMDocument page content
     *
     * @var bool
     */
    private $domDocument;

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
        if (!$this->isUrlCorrect($url)) {
            throw new IncorrectUrlException();
        }

        $this->domDocument = $this->init($url);
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
     * Parse DOM images.
     */
    public function parse()
    {
        foreach ($this->domDocument->getElementsByTagName('img') as $image) {
            foreach ($image->getAttribute('img') as $img) {
                if (!$this->isImageCorrect($img)) {
                    continue;
                }

                $this->saveImageToLocalStorage($img->src);
            }
        }
    }

    /**
     * Checks url for correctly value.
     *
     * @param string $url
     * @return bool
     */
    private function isUrlCorrect(string $url): bool
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

    /**
     * Init method, creating DOM document.
     *
     * @param string $url
     * @return \DOMDocument
     */
    private function init(string $url): \DOMDocument
    {
        $request = curl_init();
        curl_setopt_array($request, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
        ]);
        $response = curl_exec($request);
        curl_close($request);

        $document = new \DOMDocument();

        if ($response) {
            libxml_use_internal_errors(true);
            $document->loadHTML($response);
            libxml_clear_errors();
        }

        return $document;
    }
}
