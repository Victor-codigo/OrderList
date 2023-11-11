<?php

declare(strict_types=1);

namespace Shop\Application\ShopGetData\Dto;

use Common\Domain\Model\ValueObject\Constraints\VALUE_OBJECTS_CONSTRAINTS;
use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\String\NameWithSpaces;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\Service\ServiceInputDtoInterface;
use Common\Domain\Validation\ValidationInterface;

class ShopGetDataInputDto implements ServiceInputDtoInterface
{
    private const SHOP_NAME_STARTS_BY_LENGTH_MAX = VALUE_OBJECTS_CONSTRAINTS::NAME_WITH_SPACES_MAX_LENGTH;

    public readonly Identifier $groupId;
    /**
     * @var Identifier[]
     */
    public readonly array $shopsId;
    /**
     * @var Identifier[]
     */
    public readonly array $productsId;
    public readonly string|null $shopNameStartsWith;
    public readonly NameWithSpaces|null $shopName;

    public function __construct(string|null $groupId, array|null $shopsId, array|null $productsId, string|null $shopNameStartsWith, string|null $shopName)
    {
        $this->groupId = ValueObjectFactory::createIdentifier($groupId);
        $this->shopNameStartsWith = $shopNameStartsWith;
        $this->shopName = ValueObjectFactory::createNameWithSpaces($shopName);
        $this->shopsId = array_map(
            fn (string $shopId) => ValueObjectFactory::createIdentifier($shopId),
            $shopsId ?? []
        );
        $this->productsId = array_map(
            fn (string $productId) => ValueObjectFactory::createIdentifier($productId),
            $productsId ?? []
        );
    }

    public function validate(ValidationInterface $validator): array
    {
        $errorList = $validator->validateValueObjectArray(['group_id' => $this->groupId]);
        $errorListShopsId = $validator->validateValueObjectArray($this->shopsId);
        $errorListProductsId = $validator->validateValueObjectArray($this->productsId);

        if (null !== $this->shopNameStartsWith) {
            $errorListShopNameStartsWith = $validator
                ->setValue($this->shopNameStartsWith)
                ->stringMax(self::SHOP_NAME_STARTS_BY_LENGTH_MAX)
                ->validate();
        }

        if (!$this->shopName->isNull()) {
            $errorListShopName = $validator->validateValueObject($this->shopName);
        }

        if (!empty($errorListShopName)) {
            $errorList['shop_name'] = $errorListShopName;
        }

        if (!empty($errorListShopsId)) {
            $errorList['shops_id'] = $errorListShopsId;
        }

        if (!empty($errorListProductsId)) {
            $errorList['products_id'] = $errorListProductsId;
        }

        if (isset($errorListShopNameStartsWith) && !empty($errorListShopNameStartsWith)) {
            $errorList['shop_name_starts_with'] = $errorListShopNameStartsWith;
        }

        return $errorList;
    }
}
