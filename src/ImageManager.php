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
     * DOMDocument page content.
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
     * Http/https scheme.
     *
     * @var string
     */
    private $scheme;

    /**
     * Hostname.
     *
     * @var string
     */
    private $host;

    /**
     * Mapping for exif_imagetype function
     */
    private const IMAGE_EXTENSION_MAPPING = [
        1 => 'gif',
        2 => 'jpeg',
        3 => 'png',
        5 => 'psd',
        6 => 'bmp',
    ];

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

        $this->directoryPath = $this->makeImagesDirectory();
        $this->domDocument = $this->init($url);
        $this->scheme = parse_url($url, PHP_URL_SCHEME);
        $this->host = parse_url($url, PHP_URL_HOST);
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
        $imagePath = $this->directoryPath . DIRECTORY_SEPARATOR . $imageName;

        ImageFileFactory::createFromArray([
            'path' => $imagePath,
            'type' => $imageType,
            'filePath' => $filePath
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
            $imageSrc = $image->getAttribute('src');

            if (!$this->isImageCorrect($imageSrc)) {
                continue;
            }

            $this->saveImageToLocalStorage($this->generateAbsoluteUrl($imageSrc));
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
        $imageNamePrefix = !empty(pathinfo($src, PATHINFO_EXTENSION)) ? self::IMAGE_EXTENSION_MAPPING[exif_imagetype($src)] : 'jpg';

        return time() . '.' . $imageNamePrefix;
    }

    /**
     * Checks if image is correct.
     *
     * @param string $src
     * @return bool
     */
    private function isImageCorrect(string $src): bool
    {
        if (empty($src)) {
            return false;
        }

        $imageType = exif_imagetype($this->generateAbsoluteUrl($src));

        return in_array($imageType, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]);
    }

    /**
     * Generates absolute url
     *
     * @param string $src
     * @return string|null
     */
    private function generateAbsoluteUrl(string $src): ?string
    {
        if ($this->isUrlCorrect($src)) {
            return $src;
        }

        if (0 === strpos($src, '//')) {
            $resultPath = str_replace('//', $this->scheme . '://', $src);
        }

        if (0 === strpos($src, '/')) {
            $resultPath = ltrim($src, '/');
            $resultPath = $this->scheme . '://' . $this->host .'/'. $resultPath;
        }

        return $resultPath;
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
