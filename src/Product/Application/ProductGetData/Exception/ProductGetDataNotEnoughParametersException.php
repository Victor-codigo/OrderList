<?php

declare(strict_types=1);

namespace Product\Application\ProductGetData\Exception;

use Common\Domain\Exception\DomainExceptionOutput;
use Common\Domain\Response\RESPONSE_STATUS;
use Common\Domain\Response\RESPONSE_STATUS_HTTP;

class ProductGetDataNotEnoughParametersException extends DomainExceptionOutput
{
    public static function fromMessage(string $message): static
    {
        return new static($message, ['not_enough_parameters' => $message], RESPONSE_STATUS::ERROR, RESPONSE_STATUS_HTTP::BAD_REQUEST);
    }
}
