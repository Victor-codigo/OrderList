<?php

declare(strict_types=1);

namespace Test\Unit\Group\Adapter\Database\Orm\Doctrine\Repository;

use App\Group\Domain\Model\GROUP_TYPE;
use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBConnectionException;
use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBNotFoundException;
use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBUniqueConstraintException;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\Persistence\ObjectManager;
use Group\Adapter\Database\Orm\Doctrine\Repository\GroupRepository;
use Group\Domain\Model\Group;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Test\Unit\DataBaseTestCase;

class GroupRepositoryTest extends DataBaseTestCase
{
    use RefreshDatabaseTrait;

    private GroupRepository $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = $this->entityManager->getRepository(Group::class);
    }

    private function getNewGroup(): Group
    {
        return Group::fromPrimitives(
            'ddb1a26e-ed57-4e83-bc5c-0ee6f6f5c8f8',
            'New group',
            GROUP_TYPE::USER,
            'This is a new group'
        );
    }

    private function getExistsGroup(): Group
    {
        return Group::fromPrimitives(
            'fdb242b4-bac8-4463-88d0-0941bb0beee0',
            'Group One',
            GROUP_TYPE::GROUP,
            'This is a group of users'
        );
    }

    /** @test */
    public function itShouldSaveTheGroupInDatabase(): void
    {
        $groupNew = $this->getNewGroup();
        $this->object->save($groupNew);

        $userSaved = $this->object->findOneBy(['id' => $groupNew->getId()]);

        $this->assertSame($groupNew, $userSaved);
    }

    /** @test */
    public function itShouldFailIdAlreadyExists()
    {
        $this->expectException(DBUniqueConstraintException::class);

        $this->object->save($this->getExistsGroup());
    }

    /** @test */
    public function itShouldFailDataBaseError(): void
    {
        $this->expectException(DBConnectionException::class);

        /** @var MockObject|ObjectManager $objectManagerMock */
        $objectManagerMock = $this->createMock(ObjectManager::class);
        $objectManagerMock
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(ConnectionException::driverRequired(''));

        $this->mockObjectManager($this->object, $objectManagerMock);
        $this->object->save($this->getNewGroup());
    }

    /** @test */
    public function itShouldFindAGroupById(): void
    {
        $groupId = $this->getExistsGroup()->getId();
        $return = $this->object->findGroupByIdOrFail($groupId);

        $this->assertEquals($groupId, $return->getId());
    }

    /** @test */
    public function itShouldFailNoIdGroup(): void
    {
        $this->expectException(DBNotFoundException::class);

        $this->object->findGroupByIdOrFail(ValueObjectFactory::createIdentifier('0b13e52d-b058-32fb-8507-10dec634a07A'));
    }
}
