<?php

declare(strict_types=1);

namespace Common\Adapter\Jwt\Exception;

use Common\Domain\Exception\DomainException;

class JwtException extends DomainException
{
    public static function fromMessage(string $message): static
    {
        return new static($message);
    }
}
