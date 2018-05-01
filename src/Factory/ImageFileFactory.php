<?php

namespace vrba\App\Factory;

/**
 * Class ImageFileFactory
 *
 * @package vrba\App\Factory
 */
class ImageFileFactory
{
    /**
     * Creates image file from params array.
     *
     * @param array $arguments
     * @return bool
     */
    public static function createFromArray(array $arguments)
    {
        $imagePath = $arguments['path'];
        $type = $arguments['type'];
        $filePath = $arguments['filePath'];

        switch ($type) {
            case IMAGETYPE_GIF:
                $img = imageCreateFromGif($filePath);
                $status = imagegif($img, $imagePath);
                break;
            case IMAGETYPE_JPEG:
                $img = imageCreateFromJpeg($filePath);
                $status = imagejpeg($img, $imagePath);
                break;
            case IMAGETYPE_PNG:
                $img = imageCreateFromPng($filePath);
                $status = imagepng($img, $imagePath);
                break;
        }

        imagedestroy($img);

        return $status;
    }
}