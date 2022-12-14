<?php

declare(strict_types=1);

namespace Test\Unit\Common\Domain\Event\Fixtures;

use Common\Domain\Event\EventDomainInterface;
use DateTimeImmutable;

class CustomEvent implements EventDomainInterface
{
    public function __invoke(EventDomainInterface $event): void
    {
    }

    public function getOccurreddOn(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
