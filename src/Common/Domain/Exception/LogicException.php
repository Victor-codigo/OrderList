<?php

declare(strict_types=1);

namespace Common\Domain\Exception;

use LogicException as NativeLogicException;
use Throwable;

class LogicException extends NativeLogicException
{
    public static function fromMessage(string $message = '', int $code = 0, Throwable|null $previous = null): static
    {
        return new static($message, $code, $previous);
    }
}
