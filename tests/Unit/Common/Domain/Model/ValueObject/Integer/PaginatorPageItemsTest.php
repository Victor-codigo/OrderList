<?php

declare(strict_types=1);

namespace Test\Unit\Common\Domain\Model\ValueObject\Integer;

use Common\Adapter\Validation\ValidationChain;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\Validation\VALIDATION_ERRORS;
use Common\Domain\Validation\ValidationInterface;
use PHPUnit\Framework\TestCase;

class PaginatorPageItemsTest extends TestCase
{
    private ValidationInterface $validation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validation = new ValidationChain();
    }

    /** @test */
    public function itShouldValidatePageItemsIsOne(): void
    {
        $object = ValueObjectFactory::createPaginatorPageItems(1);
        $return = $this->validation->validateValueObject($object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldFailPageItemsIsNull(): void
    {
        $object = ValueObjectFactory::createPaginatorPageItems(null);
        $return = $this->validation->validateValueObject($object);

        $this->assertEquals([VALIDATION_ERRORS::NOT_BLANK, VALIDATION_ERRORS::NOT_NULL], $return);
    }

    /** @test */
    public function itShouldFailPageItemsIsZero(): void
    {
        $object = ValueObjectFactory::createPaginatorPageItems(0);
        $return = $this->validation->validateValueObject($object);

        $this->assertEquals([VALIDATION_ERRORS::GREATER_THAN], $return);
    }

    /** @test */
    public function itShouldFailPageItemsGraterThan100(): void
    {
        $object = ValueObjectFactory::createPaginatorPageItems(101);
        $return = $this->validation->validateValueObject($object);

        $this->assertEquals([VALIDATION_ERRORS::LESS_THAN_OR_EQUAL], $return);
    }
}
