<?php

declare(strict_types=1);

namespace Test\Unit\User\Adapter\Database\Orm\Doctrine\Repository;

use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBConnectionException;
use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBNotFoundException;
use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBUniqueConstraintException;
use Common\Domain\Model\ValueObject\String\Email;
use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\Persistence\ObjectManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Test\Unit\DataBaseTestCase;
use User\Adapter\Database\Orm\Doctrine\Repository\UserRepository;
use User\Domain\Model\USER_ROLES;
use User\Domain\Model\User;

class UserRepositoryTest extends DataBaseTestCase
{
    use RefreshDatabaseTrait;

    private const USER_ID = '1befdbe2-9c14-42f0-850f-63e061e33b8f';
    private const USER_EMAIL = 'email.already.exists@host.com';

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /** @test */
    public function itShouldSaveTheUserInDataBase(): void
    {
        $userNew = $this->getNewUser();
        $this->userRepository->save($userNew);
        $userSaved = $this->userRepository->findOneBy(['id' => $userNew->getId()]);

        $this->assertSame($userNew, $userSaved);
    }

    /** @test */
    public function itShouldFailEmailAlreadyExists()
    {
        $this->expectException(DBUniqueConstraintException::class);

        $this->userRepository->save($this->getExsitsUser());
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

        $this->mockObjectManager($this->userRepository, $objectManagerMock);
        $this->userRepository->save($this->getNewUser());
    }

    /** @test */
    public function itShouldFindAUserById(): void
    {
        $userId = new Identifier(self::USER_ID);
        $return = $this->userRepository->findUserByIdOrFail($userId);

        $this->assertEquals($userId, $return->getId());
    }

    /** @test */
    public function itShouldFailFindingAUserById(): void
    {
        $this->expectException(DBNotFoundException::class);

        $userId = new Identifier(self::USER_ID.'-Not valid id');
        $this->userRepository->findUserByIdOrFail($userId);
    }

    /** @test */
    public function itShouldFindAUserByIdNoCache(): void
    {
        $userId = new Identifier(self::USER_ID);
        $return = $this->userRepository->findUserByIdOrFail($userId);
        $return->setEmail(ValueObjectFactory::createEmail('other.email@host.com'));
        $returnNoCache = $this->userRepository->findUserByIdNoCacheOrFail($userId);

        $this->assertEquals($return->getEmail(), $returnNoCache->getEmail());
    }

    /** @test */
    public function itShouldFindAUserByEmail(): void
    {
        $userEmail = new Email(self::USER_EMAIL);
        $return = $this->userRepository->findUserByEmailOrFail($userEmail);

        $this->assertEquals($userEmail, $return->getEmail());
    }

    /** @test */
    public function itShouldFailFindingAUserByEmail(): void
    {
        $this->expectException(DBNotFoundException::class);

        $userEmail = new Email(self::USER_EMAIL.'-Not valid email');
        $this->userRepository->findUserByEmailOrFail($userEmail);
    }

    /** @test */
    public function itShouldReturnManyUsersById(): void
    {
        $usersId = [
            ValueObjectFactory::createIdentifier('0b13e52d-b058-32fb-8507-10dec634a07c'),
            ValueObjectFactory::createIdentifier('0b17ca3e-490b-3ddb-aa78-35b4ce668dc0'),
            ValueObjectFactory::createIdentifier('1befdbe2-9c14-42f0-850f-63e061e33b8f'),
        ];
        $return = $this->userRepository->findUsersByIdOrFail($usersId);
        $dbUsersIds = array_map(
            fn (User $user) => $user->getId()->getValue(),
            $return
        );

        $this->assertContainsOnlyInstancesOf(User::class, $return);
        $this->assertCount(count($usersId), $return);
        $this->assertEquals($dbUsersIds, $usersId);
    }

    /** @test */
    public function itShouldFailNoIds(): void
    {
        $this->expectException(DBNotFoundException::class);

        $this->userRepository->findUsersByIdOrFail([]);
    }

    /** @test */
    public function itShouldFailIdsDoesNotExistsInDataBase(): void
    {
        $this->expectException(DBNotFoundException::class);

        $usersId = [
            ValueObjectFactory::createIdentifier('0b13e52d-b058-32fb-8507-10dec634a07A'),
            ValueObjectFactory::createIdentifier('0b17ca3e-490b-3ddb-aa78-35b4ce668dcA'),
            ValueObjectFactory::createIdentifier('1befdbe2-9c14-42f0-850f-63e061e33b8A'),
        ];
        $this->userRepository->findUsersByIdOrFail($usersId);
    }

    private function getNewUser(): User
    {
        return User::fromPrimitives(
            '86c304df-a63e-4083-b1ee-add73be940a3',
            'new.user.email@host.com',
            'this is my passorwd',
            'Alfredo',
            [USER_ROLES::NOT_ACTIVE]
        );
    }

    private function getExsitsUser(): User
    {
        return User::fromPrimitives(
            '1befdbe2-9c14-42f0-850f-63e061e33b8f',
            'email.already.exists@host.com',
            'qwerty',
            'Juanito',
            [USER_ROLES::NOT_ACTIVE]
        );
    }
}
