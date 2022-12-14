<?php

declare(strict_types=1);

namespace Test\Unit\User\Domain\Service\GetUsersProfilePublicData;

use Common\Domain\Database\Orm\Doctrine\Repository\Exception\DBNotFoundException;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\Struct\SCOPE;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use User\Domain\Model\Profile;
use User\Domain\Port\Repository\ProfileRepositoryInterface;
use User\Domain\Service\GetUsersProfilePublicData\Dto\GetUsersProfilePublicDataDto;
use User\Domain\Service\GetUsersProfilePublicData\Dto\GetUsersProfilePublicDataOutputDto;
use User\Domain\Service\GetUsersProfilePublicData\GetUsersProfilePublicDataService;

class GetUsersProfilePublcDataServiceTest extends TestCase
{
    private const URL_IMAGE_PUBLIC_PATH = 'URL_PUBLIC_IMAGE';

    private GetUsersProfilePublicDataService $object;
    private MockObject|ProfileRepositoryInterface $profileRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profileRepository = $this->createMock(ProfileRepositoryInterface::class);
        $this->object = new GetUsersProfilePublicDataService($this->profileRepository, self::URL_IMAGE_PUBLIC_PATH);
    }

    private function getProfilesId(): array
    {
        return [
            ValueObjectFactory::createIdentifier('0b13e52d-b058-32fb-8507-10dec634a07c'),
            ValueObjectFactory::createIdentifier('0b17ca3e-490b-3ddb-aa78-35b4ce668dc0'),
            ValueObjectFactory::createIdentifier('1befdbe2-9c14-42f0-850f-63e061e33b8f'),
        ];
    }

    private function getProfiles(): array
    {
        return [
            new Profile(ValueObjectFactory::createIdentifier('0b13e52d-b058-32fb-8507-10dec634a07c'), ValueObjectFactory::createPath('path\\to\\file')),
            new Profile(ValueObjectFactory::createIdentifier('0b17ca3e-490b-3ddb-aa78-35b4ce668dc0'), ValueObjectFactory::createPath('path\\to\\file')),
            new Profile(ValueObjectFactory::createIdentifier('1befdbe2-9c14-42f0-850f-63e061e33b8f'), ValueObjectFactory::createPath('path\\to\\file')),
        ];
    }

    /** @test */
    public function itShouldGetTheUsersProfilePublicData(): void
    {
        $profilesId = $this->getProfilesId();
        $expectedProfiles = $this->getProfiles();
        $expectedProfileIdentifiers = array_map(fn (Profile $profile) => $profile->getId(), $expectedProfiles);
        $expectedProfileImages = array_map(
            fn (Profile $profile) => ValueObjectFactory::createPath(self::URL_IMAGE_PUBLIC_PATH.'/'.$profile->getImage()->getValue()),
            $expectedProfiles
        );

        $this->profileRepository
            ->expects($this->once())
            ->method('findProfilesOrFail')
            ->with($profilesId)
            ->willReturn($expectedProfiles);

        $profilesDto = new GetUsersProfilePublicDataDto($profilesId);
        $return = $this->object->__invoke($profilesDto, SCOPE::PUBLIC);

        $this->assertInstanceOf(GetUsersProfilePublicDataOutputDto::class, $return);

        foreach ($return->profileData as $profile) {
            $this->assertArrayHasKey('id', $profile);
            $this->assertArrayHasKey('image', $profile);
            $this->assertContains($profile['id'], $expectedProfileIdentifiers);
            $this->assertContainsEquals($profile['image'], $expectedProfileImages);
        }
    }

    /** @test */
    public function itShouldGetTheUsersProfilePrivateData(): void
    {
        $profilesId = $this->getProfilesId();
        $expectedProfiles = $this->getProfiles();
        $expectedProfileIdentifiers = array_map(fn (Profile $profile) => $profile->getId(), $expectedProfiles);
        $expectedProfileImages = array_map(
            fn (Profile $profile) => ValueObjectFactory::createPath(self::URL_IMAGE_PUBLIC_PATH.'/'.$profile->getImage()->getValue()),
            $expectedProfiles
        );

        $this->profileRepository
            ->expects($this->once())
            ->method('findProfilesOrFail')
            ->with($profilesId)
            ->willReturn($expectedProfiles);

        $profilesDto = new GetUsersProfilePublicDataDto($profilesId);
        $return = $this->object->__invoke($profilesDto, SCOPE::PRIVATE);

        $this->assertInstanceOf(GetUsersProfilePublicDataOutputDto::class, $return);

        foreach ($return->profileData as $profile) {
            $this->assertArrayHasKey('id', $profile);
            $this->assertArrayHasKey('image', $profile);
            $this->assertContains($profile['id'], $expectedProfileIdentifiers);
            $this->assertContainsEquals($profile['image'], $expectedProfileImages);
        }
    }

    /* @test */
    public function itShouldFailNoUsersProfileFound(): void
    {
        $this->expectException(DBNotFoundException::class);

        $this->profileRepository
            ->expects($this->once())
            ->method('findProfilesOrFail')
            ->with([])
            ->willThrowException(DBNotFoundException::fromMessage(''));

        $usersProfileDto = new GetUsersProfilePublicDataDto([]);
        $this->object->__invoke($usersProfileDto, SCOPE::PRIVATE);
    }
}
