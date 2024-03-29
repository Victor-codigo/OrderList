<?php

declare(strict_types=1);

namespace Common\Domain\Model\ValueObject\String;

class ValueObjectStringFactory
{
    public static function createEmail(string|null $email): Email
    {
        return new Email($email);
    }

    public static function createIdentifier(string|null $id): Identifier
    {
        return new Identifier($id);
    }

    public static function createIdentifierNullAble(string|null $id): IdentifierNullable
    {
        return new IdentifierNullable($id);
    }

    public static function createName(string|null $name): Name
    {
        return new Name($name);
    }

    public static function createNameWithSpaces(string|null $name): NameWithSpaces
    {
        return new NameWithSpaces($name);
    }

    public static function createDescription(string|null $description): Description
    {
        return new Description($description);
    }

    public static function createPassword(string|null $password): Password
    {
        return new Password($password);
    }

    public static function createPath(string|null $path): Path
    {
        return new Path($path);
    }

    public static function createJwtToken(string|null $path): JwtToken
    {
        return new JwtToken($path);
    }

    public static function createUrl(string|null $url): Url
    {
        return new Url($url);
    }

    public static function createLanguage(string|null $lang): Language
    {
        return new Language($lang);
    }
}
