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
        $path = $arguments['path'];
        $type = $arguments['type'];
        $name = $arguments['name'];

        $imagePath = $path . DIRECTORY_SEPARATOR . $name;

        switch ($type) {
            case IMAGETYPE_GIF:
                $img = imageCreateFromGif($path);

                return imagegif($img, $imagePath . '.gif');
            case IMAGETYPE_JPEG:
                $img = imageCreateFromJpeg($path);

                return imagejpeg($img, $imagePath . '.jpeg');
            case IMAGETYPE_PNG:
                $img = imageCreateFromPng($path);

                return imagepng($img, $imagePath . '.png');
        }
    }
}