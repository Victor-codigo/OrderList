<?php

declare(strict_types=1);

namespace Group\Domain\Port;

use Common\Domain\Model\ValueObject\String\Identifier;
use Group\Domain\Model\Group;

interface GroupRepositoryInterface
{
    /**
     * @throws DBUniqueConstraintException
     * @throws DBConnectionException
     */
    public function save(Group $group): void;

    /**
     * @throws DBNotFoundException
     */
    public function findGroupByIdOrFail(Identifier $id): Group;
}
