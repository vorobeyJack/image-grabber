<?php

namespace vrba\App\Exception;

use Throwable;

/**
 * Class IncorrectUrlException
 *
 * @package vrba\App\Exception
 */
class IncorrectUrlException extends \Exception
{
    /**
     * Default exception message.
     */
    private const MESSAGE = 'Current url is not correct';

    /**
     * IncorrectUrlException constructor.
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