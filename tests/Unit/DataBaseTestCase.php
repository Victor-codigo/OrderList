<?php

declare(strict_types=1);

namespace Test\Unit;

use Common\Domain\Exception\LogicException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DataBaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface|null $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::bootKernel();

        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Only executable in test enviroment');
        }

        $this->entityManager = $kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    protected function mockObjectManager(ServiceEntityRepositoryInterface $repository, MockObject|ObjectManager $objectManagerMock): void
    {
        $userRepositoryReflection = new ReflectionClass($repository);
        $objectManagerProperty = $userRepositoryReflection->getProperty('objectManager');
        $objectManagerProperty->setValue($repository, $objectManagerMock);
    }
}
