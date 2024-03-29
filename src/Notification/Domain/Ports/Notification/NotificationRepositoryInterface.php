<?php

declare(strict_types=1);

namespace Notification\Domain\Ports\Notification;

use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Ports\Paginator\PaginatorInterface;
use Common\Domain\Ports\Repository\RepositoryInterface;
use Notification\Domain\Model\Notification;

interface NotificationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param Notification[] $notifications
     *
     * @throws DBUniqueConstraintException
     * @throws DBConnectionException
     */
    public function save(array $notifications): void;

    /**
     * @param Notification[] $notifications
     *
     * @throws DBConnectionException
     */
    public function remove(array $notifications): void;

    /**
     * @param Identifier[] $notificationsId
     *
     * @throws DBNotFoundException
     */
    public function getNotificationsByIdOrFail(array $notificationsId): PaginatorInterface;

    /**
     * @param Identifier[] $userId
     *
     * @throws DBNotFoundException
     */
    public function getNotificationByUserIdOrFail(Identifier $userId): PaginatorInterface;
}
