<?php

declare(strict_types=1);

namespace Shop\Domain\Port\Repository;

use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\String\NameWithSpaces;
use Common\Domain\Ports\Paginator\PaginatorInterface;
use Common\Domain\Ports\Repository\RepositoryInterface;
use Shop\Domain\Model\Shop;

interface ShopRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws DBUniqueConstraintException
     * @throws DBConnectionException
     */
    public function save(Shop $shops): void;

    /**
     * @param Shop[] $shops
     *
     * @throws DBConnectionException
     */
    public function remove(array $shops): void;

    /**
     * @throws DBNotFoundException
     */
    public function findShopsByGroupAndNameOrFail(Identifier $groupId, NameWithSpaces $name): PaginatorInterface;

    /**
     * @param Identifier[]|null $shopsId
     * @param Identifier[]|null $productsId
     *
     * @throws DBNotFoundException
     */
    public function findShopsOrFail(array|null $shopsId = null, Identifier|null $groupId = null, array|null $productId = null, string|null $shopNameStartsWith = null): PaginatorInterface;
}