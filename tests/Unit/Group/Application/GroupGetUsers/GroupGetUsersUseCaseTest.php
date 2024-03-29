<?php

declare(strict_types=1);

namespace Test\Unit\Group\Application\GroupGetUsers;

use Common\Adapter\ModuleCommunication\Exception\ModuleCommunicationException;
use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBNotFoundException;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Model\ValueObject\Object\Rol;
use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\ModuleCommunication\ModuleCommunicationConfigDto;
use Common\Domain\ModuleCommunication\ModuleCommunicationFactory;
use Common\Domain\Ports\ModuleCommunication\ModuleCommunicationInterface;
use Common\Domain\Ports\Paginator\PaginatorInterface;
use Common\Domain\Response\RESPONSE_STATUS;
use Common\Domain\Response\ResponseDto;
use Common\Domain\Security\UserShared;
use Common\Domain\Service\Exception\DomainErrorException;
use Common\Domain\Validation\Group\GROUP_ROLES;
use Common\Domain\Validation\User\USER_ROLES;
use Common\Domain\Validation\ValidationInterface;
use Group\Application\GroupGetUsers\Dto\GroupGetUsersInputDto;
use Group\Application\GroupGetUsers\Dto\GroupGetUsersOutputDto;
use Group\Application\GroupGetUsers\Exception\GroupGetUsersGroupNotFoundException;
use Group\Application\GroupGetUsers\Exception\GroupGetUsersUserNotInTheGroupException;
use Group\Application\GroupGetUsers\GroupGetUsersUseCase;
use Group\Domain\Model\Group;
use Group\Domain\Model\UserGroup;
use Group\Domain\Port\Repository\UserGroupRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GroupGetUsersUseCaseTest extends TestCase
{
    private const GROUP_ID = 'fdb242b4-bac8-4463-88d0-0941bb0beee0';

    private GroupGetUsersUseCase $object;
    private MockObject|UserGroupRepositoryInterface $userGroupRepository;
    private MockObject|ModuleCommunicationInterface $moduleCommunication;
    private MockObject|ValidationInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userGroupRepository = $this->createMock(UserGroupRepositoryInterface::class);
        $this->moduleCommunication = $this->createMock(ModuleCommunicationInterface::class);
        $this->validator = $this->createMock(ValidationInterface::class);
        $this->object = new GroupGetUsersUseCase($this->userGroupRepository, $this->moduleCommunication, $this->validator);
    }

    private function getUserSession(): UserShared
    {
        return UserShared::fromPrimitives('2606508b-4516-45d6-93a6-c7cb416b7f3f', 'user@emil.com', 'UserName', [USER_ROLES::USER], null, new \DateTime());
    }

    private function getUsersGroup(): MockObject|PaginatorInterface
    {
        $group = $this->createMock(Group::class);
        $userGroups = [
            UserGroup::fromPrimitives(self::GROUP_ID, '1befdbe2-9c14-42f0-850f-63e061e33b8f', [GROUP_ROLES::USER], $group),
            UserGroup::fromPrimitives(self::GROUP_ID, '08eda546-739f-4ab7-917a-8a9dbee426ef', [GROUP_ROLES::USER], $group),
            UserGroup::fromPrimitives(self::GROUP_ID, '6df60afd-f7c3-4c2c-b920-e265f266c560', [GROUP_ROLES::USER], $group),
        ];

        /** @var MockObject|PaginatorInterface $paginator */
        $paginator = $this->createMock(PaginatorInterface::class);
        $paginator
            ->expects($this->any())
            ->method('getIterator')
            ->willReturnCallback(function () use ($userGroups) {
                foreach ($userGroups as $userGroup) {
                    yield $userGroup;
                }
            });

        $paginator
            ->expects($this->any())
            ->method('count')
            ->willReturn(count($userGroups));

        return $paginator;
    }

    private function getUsersGroupData(): array
    {
        return [
            ['id' => '1befdbe2-9c14-42f0-850f-63e061e33b8f', 'name' => 'user1'],
            ['id' => '08eda546-739f-4ab7-917a-8a9dbee426ef', 'name' => 'user2'],
            ['id' => '6df60afd-f7c3-4c2c-b920-e265f266c560', 'name' => 'user3'],
        ];
    }

    /**
     * @param Identifier[] $userId
     */
    private function getModuleCommunicationConfigDto(array $userId): ModuleCommunicationConfigDto
    {
        return ModuleCommunicationFactory::userGet($userId);
    }

    /** @test */
    public function itShouldGetAllUsersOfTheGroup(): void
    {
        $userSession = $this->getUserSession();
        $usersGroup = $this->getUsersGroup();
        $usersGroupId = array_map(fn (UserGroup $userGroup) => $userGroup->getUserId(), iterator_to_array($usersGroup));
        $usersGroupIdPlain = array_map(fn (UserGroup $userGroup) => $userGroup->getUserId()->getValue(), iterator_to_array($usersGroup));
        $usersGroupAdmin = array_map(fn (UserGroup $userGroup) => $userGroup->getRoles()->has(new Rol(GROUP_ROLES::ADMIN)), iterator_to_array($usersGroup));
        $usersGroupData = $this->getUsersGroupData();
        $usersGroupNames = array_column($usersGroupData, 'name');

        $moduleCommunicationConfigDto = $this->getModuleCommunicationConfigDto($usersGroupId);
        $responseDtoGetUsers = new ResponseDto($usersGroupData, [], '', RESPONSE_STATUS::OK, true);
        $groupId = ValueObjectFactory::createIdentifier(self::GROUP_ID);

        $pageItems = 50;
        $page = 0;
        $input = new GroupGetUsersInputDto($userSession, self::GROUP_ID, $pageItems, $page);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersOrFail')
            ->with($groupId)
            ->willReturn($usersGroup);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersByUserIdOrFail')
            ->with($groupId, [$userSession->getId()])
            ->willReturn([$userSession]);

        $this->moduleCommunication
            ->expects($this->once())
            ->method('__invoke')
            ->with($moduleCommunicationConfigDto)
            ->willReturn($responseDtoGetUsers);

        $return = $this->object->__invoke($input);

        $this->assertInstanceOf(GroupGetUsersOutputDto::class, $return);
        $this->assertCount(count($usersGroup), $return->users);

        foreach ($return->users as $key => $user) {
            $this->assertCount(3, $user);
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('name', $user);
            $this->assertArrayHasKey('admin', $user);
            $this->assertContains($user['id'], $usersGroupIdPlain);
            $this->assertContains($user['name'], $usersGroupNames);
            $this->assertEquals($user['admin'], $usersGroupAdmin[$key]);
        }
    }

    /** @test */
    public function itShouldFailGroupHasNoUsers(): void
    {
        $userSession = $this->getUserSession();
        $groupId = ValueObjectFactory::createIdentifier(self::GROUP_ID);
        $pageItems = 50;
        $page = 0;
        $input = new GroupGetUsersInputDto($userSession, self::GROUP_ID, $pageItems, $page);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersOrFail')
            ->with($groupId)
            ->willThrowException(new DBNotFoundException());

        $this->userGroupRepository
            ->expects($this->never())
            ->method('findGroupUsersByUserIdOrFail');

        $this->moduleCommunication
            ->expects($this->never())
            ->method('__invoke');

        $this->expectException(GroupGetUsersGroupNotFoundException::class);
        $this->object->__invoke($input);
    }

    /** @test */
    public function itShouldFailUserSessionIsNotInTheGroup(): void
    {
        $userSession = $this->getUserSession();
        $usersGroup = $this->getUsersGroup();

        $groupId = ValueObjectFactory::createIdentifier(self::GROUP_ID);

        $pageItems = 50;
        $page = 0;
        $input = new GroupGetUsersInputDto($userSession, self::GROUP_ID, $pageItems, $page);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersOrFail')
            ->with($groupId)
            ->willReturn($usersGroup);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersByUserIdOrFail')
            ->with($groupId, [$userSession->getId()])
            ->willThrowException(new DBNotFoundException());

        $this->moduleCommunication
            ->expects($this->never())
            ->method('__invoke');

        $this->expectException(GroupGetUsersUserNotInTheGroupException::class);
        $this->object->__invoke($input);
    }

    /** @test */
    public function itShouldFailModuleCommunicationError400(): void
    {
        $userSession = $this->getUserSession();
        $usersGroup = $this->getUsersGroup();
        $usersGroupId = array_map(fn (UserGroup $userGroup) => $userGroup->getUserId(), iterator_to_array($usersGroup));

        $moduleCommunicationConfigDto = $this->getModuleCommunicationConfigDto($usersGroupId);
        $groupId = ValueObjectFactory::createIdentifier(self::GROUP_ID);

        $pageItems = 50;
        $page = 0;
        $input = new GroupGetUsersInputDto($userSession, self::GROUP_ID, $pageItems, $page);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersOrFail')
            ->with($groupId)
            ->willReturn($usersGroup);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersByUserIdOrFail')
            ->with($groupId, [$userSession->getId()])
            ->willReturn([$userSession]);

        $this->moduleCommunication
            ->expects($this->once())
            ->method('__invoke')
            ->with($moduleCommunicationConfigDto)
            ->willThrowException(new Error400Exception());

        $this->expectException(DomainErrorException::class);
        $this->object->__invoke($input);
    }

    /** @test */
    public function itShouldFailModuleCommunicationException(): void
    {
        $userSession = $this->getUserSession();
        $usersGroup = $this->getUsersGroup();
        $usersGroupId = array_map(fn (UserGroup $userGroup) => $userGroup->getUserId(), iterator_to_array($usersGroup));

        $moduleCommunicationConfigDto = $this->getModuleCommunicationConfigDto($usersGroupId);
        $groupId = ValueObjectFactory::createIdentifier(self::GROUP_ID);

        $pageItems = 50;
        $page = 0;
        $input = new GroupGetUsersInputDto($userSession, self::GROUP_ID, $pageItems, $page);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersOrFail')
            ->with($groupId)
            ->willReturn($usersGroup);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersByUserIdOrFail')
            ->with($groupId, [$userSession->getId()])
            ->willReturn([$userSession]);

        $this->moduleCommunication
            ->expects($this->once())
            ->method('__invoke')
            ->with($moduleCommunicationConfigDto)
            ->willThrowException(new ModuleCommunicationException());

        $this->expectException(DomainErrorException::class);
        $this->object->__invoke($input);
    }

    /** @test */
    public function itShouldFailModuleCommunicationValueError(): void
    {
        $userSession = $this->getUserSession();
        $usersGroup = $this->getUsersGroup();
        $usersGroupId = array_map(fn (UserGroup $userGroup) => $userGroup->getUserId(), iterator_to_array($usersGroup));

        $moduleCommunicationConfigDto = $this->getModuleCommunicationConfigDto($usersGroupId);
        $groupId = ValueObjectFactory::createIdentifier(self::GROUP_ID);

        $pageItems = 50;
        $page = 0;
        $input = new GroupGetUsersInputDto($userSession, self::GROUP_ID, $pageItems, $page);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersOrFail')
            ->with($groupId)
            ->willReturn($usersGroup);

        $this->userGroupRepository
            ->expects($this->once())
            ->method('findGroupUsersByUserIdOrFail')
            ->with($groupId, [$userSession->getId()])
            ->willReturn([$userSession]);

        $this->moduleCommunication
            ->expects($this->once())
            ->method('__invoke')
            ->with($moduleCommunicationConfigDto)
            ->willThrowException(new \ValueError());

        $this->expectException(DomainErrorException::class);
        $this->object->__invoke($input);
    }
}
