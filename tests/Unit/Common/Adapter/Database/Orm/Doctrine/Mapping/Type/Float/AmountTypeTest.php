<?php

declare(strict_types=1);

namespace Test\Unit\Common\Adapter\Database\Orm\Doctrine\Mapping\Type\Float;

use Common\Adapter\Database\Orm\Doctrine\Mapping\Type\Float\AmountType;
use Common\Domain\Model\ValueObject\Float\Amount;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\TestCase;

class AmountTypeTest extends TestCase
{
    private AbstractPlatform $abstractPlatform;

    protected function setUp(): void
    {
        parent::setUp();

        $this->abstractPlatform = $this->createMock(AbstractPlatform::class);
    }

    /** @test */
    public function itShouldReturnAValidPhpValuePassedInt(): void
    {
        $value = 3;
        $object = new AmountType($value);
        $return = $object->convertToPHPValue($value, $this->abstractPlatform);

        $this->assertInstanceOf(Amount::class, $return);
        $this->assertEquals($value, $return->getValue());
    }

    /** @test */
    public function itShouldReturnAValidPhpValuePassedFloat(): void
    {
        $value = 3.2;
        $object = new AmountType($value);
        $return = $object->convertToPHPValue($value, $this->abstractPlatform);

        $this->assertInstanceOf(Amount::class, $return);
        $this->assertEquals($value, $return->getValue());
    }

    /** @test */
    public function itShouldReturnAValidPhpValuePassedString(): void
    {
        $value = '3.2';
        $object = new AmountType($value);
        $return = $object->convertToPHPValue($value, $this->abstractPlatform);

        $this->assertInstanceOf(Amount::class, $return);
        $this->assertEquals($value, $return->getValue());
    }
}
