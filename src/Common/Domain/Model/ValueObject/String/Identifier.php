<?php

declare(strict_types=1);

namespace Common\Domain\Model\ValueObject\String;

use Common\Domain\Validation\Common\TYPES;
use Common\Domain\Validation\ConstraintFactory;

class Identifier extends StringValueObject
{
    protected function defineConstraints(): void
    {
        $this
            ->setConstraint(ConstraintFactory::notBlank())
            ->setConstraint(ConstraintFactory::notNull())
            ->setConstraint(ConstraintFactory::type(TYPES::STRING))
            ->setConstraint(ConstraintFactory::uuId());
    }

    public function __toString(): string
    {
        if ($this->isNull()) {
            return '';
        }

        return $this->getValue();
    }
}
