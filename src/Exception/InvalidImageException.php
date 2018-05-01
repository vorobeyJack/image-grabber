<?php

namespace vrba\App\Exception;

use Throwable;

/**
 * Class InvalidImageException
 *
 * @package vrba\App\Exception
 */
class InvalidImageException extends \Exception
{
    /**
     * Default exception message.
     */
    private const MESSAGE = 'This file in not correct image';

    /**
     * InvalidImageException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = self::MESSAGE, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}