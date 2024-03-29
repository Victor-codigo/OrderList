<?php

declare(strict_types=1);

namespace Common\Domain\Model\ValueObject;

use Common\Domain\Model\ValueObject\Array\NotificationData;
use Common\Domain\Model\ValueObject\Array\Roles;
use Common\Domain\Model\ValueObject\Array\ValueObjectArrayFactoryInterface;
use Common\Domain\Model\ValueObject\Array\valueObjectArrayFactory;
use Common\Domain\Model\ValueObject\Date\DateNowToFuture;
use Common\Domain\Model\ValueObject\Date\ValueObjectDateFactory;
use Common\Domain\Model\ValueObject\Date\ValueObjectDateFactoryInterface;
use Common\Domain\Model\ValueObject\Float\Amount;
use Common\Domain\Model\ValueObject\Float\Money;
use Common\Domain\Model\ValueObject\Float\ValueObjectFloatFactory;
use Common\Domain\Model\ValueObject\Float\ValueObjectFloatFactoryInterface;
use Common\Domain\Model\ValueObject\Group\Filter;
use Common\Domain\Model\ValueObject\Group\ValueObjectGroupFactory;
use Common\Domain\Model\ValueObject\Group\ValueObjectGroupFactoryInterface;
use Common\Domain\Model\ValueObject\Integer\PaginatorPage;
use Common\Domain\Model\ValueObject\Integer\PaginatorPageItems;
use Common\Domain\Model\ValueObject\Integer\ValueObjectIntegerFactory;
use Common\Domain\Model\ValueObject\Integer\ValueObjectIntegerFactoryInterface;
use Common\Domain\Model\ValueObject\Object\File;
use Common\Domain\Model\ValueObject\Object\Filter\FilterDbLikeComparison;
use Common\Domain\Model\ValueObject\Object\Filter\FilterSection;
use Common\Domain\Model\ValueObject\Object\Filter\ValueObjectFilterInterface;
use Common\Domain\Model\ValueObject\Object\GroupImage;
use Common\Domain\Model\ValueObject\Object\GroupType;
use Common\Domain\Model\ValueObject\Object\NotificationType;
use Common\Domain\Model\ValueObject\Object\ProductImage;
use Common\Domain\Model\ValueObject\Object\Rol;
use Common\Domain\Model\ValueObject\Object\ShopImage;
use Common\Domain\Model\ValueObject\Object\UnitMeasure;
use Common\Domain\Model\ValueObject\Object\UserImage;
use Common\Domain\Model\ValueObject\Object\ValueObjectObjectFactory;
use Common\Domain\Model\ValueObject\Object\ValueObjectObjectFactoryInterface;
use Common\Domain\Model\ValueObject\String\Description;
use Common\Domain\Model\ValueObject\String\Email;
use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\String\IdentifierNullable;
use Common\Domain\Model\ValueObject\String\JwtToken;
use Common\Domain\Model\ValueObject\String\Language;
use Common\Domain\Model\ValueObject\String\Name;
use Common\Domain\Model\ValueObject\String\NameWithSpaces;
use Common\Domain\Model\ValueObject\String\Password;
use Common\Domain\Model\ValueObject\String\Path;
use Common\Domain\Model\ValueObject\String\Url;
use Common\Domain\Model\ValueObject\String\ValueObjectStringFactory;
use Common\Domain\Model\ValueObject\String\ValueObjectStringFactoryInterface;
use Common\Domain\Ports\FileUpload\FileInterface;
use Common\Domain\Validation\Filter\FILTER_SECTION;
use Common\Domain\Validation\Group\GROUP_TYPE;
use Common\Domain\Validation\Notification\NOTIFICATION_TYPE;
use Common\Domain\Validation\UnitMeasure\UNIT_MEASURE_TYPE;

final class ValueObjectFactory implements ValueObjectStringFactoryInterface, ValueObjectArrayFactoryInterface, ValueObjectObjectFactoryInterface, ValueObjectIntegerFactoryInterface, ValueObjectFloatFactoryInterface, ValueObjectDateFactoryInterface, ValueObjectGroupFactoryInterface
{
    /**
     * @param Rol[]|null $roles
     */
    public static function createRoles(array|null $roles): Roles
    {
        return valueObjectArrayFactory::createRoles($roles);
    }

    public static function createRol(\BackedEnum|null $roles): Rol
    {
        return ValueObjectObjectFactory::createRol($roles);
    }

    public static function createNotificationData(array|null $data): NotificationData
    {
        return valueObjectArrayFactory::createNotificationData($data);
    }

    public static function createEmail(string|null $email): Email
    {
        return ValueObjectStringFactory::createEmail($email);
    }

    public static function createIdentifier(string|null $id): Identifier
    {
        return ValueObjectStringFactory::createIdentifier($id);
    }

    public static function createIdentifierNullable(string|null $id): IdentifierNullable
    {
        return ValueObjectStringFactory::createIdentifierNullAble($id);
    }

    public static function createName(string|null $name): Name
    {
        return ValueObjectStringFactory::createName($name);
    }

    public static function createNameWithSpaces(string|null $name): NameWithSpaces
    {
        return ValueObjectStringFactory::createNameWithSpaces($name);
    }

    public static function createDescription(string|null $description): Description
    {
        return ValueObjectStringFactory::createDescription($description);
    }

    public static function createPassword(string|null $password): Password
    {
        return ValueObjectStringFactory::createPassword($password);
    }

    public static function createPath(string|null $path): Path
    {
        return ValueObjectStringFactory::createPath($path);
    }

    public static function createJwtToken(string|null $path): JwtToken
    {
        return ValueObjectStringFactory::createJwtToken($path);
    }

    public static function createUrl(string|null $url): Url
    {
        return ValueObjectStringFactory::createUrl($url);
    }

    public static function createLanguage(string|null $language): Language
    {
        return ValueObjectStringFactory::createLanguage($language);
    }

    public static function createFile(FileInterface|null $file): File
    {
        return ValueObjectObjectFactory::createFile($file);
    }

    public static function createUserImage(FileInterface|null $file): UserImage
    {
        return ValueObjectObjectFactory::createUserImage($file);
    }

    public static function createGroupImage(FileInterface|null $file): GroupImage
    {
        return ValueObjectObjectFactory::createGroupImage($file);
    }

    public static function createGroupType(GROUP_TYPE|null $type): GroupType
    {
        return ValueObjectObjectFactory::createGroupType($type);
    }

    public static function createPaginatorPage(int|null $page): PaginatorPage
    {
        return ValueObjectIntegerFactory::createPaginatorPage($page);
    }

    public static function createPaginatorPageItems(int|null $pageItems): PaginatorPageItems
    {
        return ValueObjectIntegerFactory::createPaginatorPageItems($pageItems);
    }

    public static function createMoney(float|null $money): Money
    {
        return ValueObjectFloatFactory::createMoney($money);
    }

    public static function createAmount(float|null $amount): Amount
    {
        return ValueObjectFloatFactory::createAmount($amount);
    }

    public static function createNotificationType(NOTIFICATION_TYPE|null $type): NotificationType
    {
        return ValueObjectObjectFactory::createNotificationType($type);
    }

    public static function createUnit(UNIT_MEASURE_TYPE|null $type): UnitMeasure
    {
        return ValueObjectObjectFactory::createUnit($type);
    }

    public static function createProductImage(FileInterface|null $type): ProductImage
    {
        return ValueObjectObjectFactory::createProductImage($type);
    }

    public static function createShopImage(FileInterface|null $type): ShopImage
    {
        return ValueObjectObjectFactory::createShopImage($type);
    }

    public static function createDateNowToFuture(\DateTime|null $date): DateNowToFuture
    {
        return ValueObjectDateFactory::createDateNowToFuture($date);
    }

    public static function createFilterDbLikeComparison(\BackedEnum|null $filter): FilterDbLikeComparison
    {
        return ValueObjectObjectFactory::createFilterDbLikeComparison($filter);
    }

    public static function createFilterSection(FILTER_SECTION|null $filter): FilterSection
    {
        return ValueObjectObjectFactory::createFilterSection($filter);
    }

    public static function createFilter(string $id, ValueObjectBase&ValueObjectFilterInterface $type, ValueObjectBase $value): Filter
    {
        return ValueObjectGroupFactory::createFilter($id, $type, $value);
    }
}
