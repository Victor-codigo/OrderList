<?php

declare(strict_types=1);

namespace Common\Domain\Model\ValueObject\Integer;

use Common\Domain\Model\ValueObject\ValueObjectBase;

abstract class IntegerValueObject extends ValueObjectBase
{
    protected readonly int $value;

    public function __construct(int $value)
    {
        $this->value = $value;

        $this->defineConstraints();
    }

    public function getValue(): int
    {
        return $this->value;
    }
}