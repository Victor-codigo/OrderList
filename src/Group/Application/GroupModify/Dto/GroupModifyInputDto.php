<?php

declare(strict_types=1);

namespace Group\Application\GroupModify\Dto;

use Common\Domain\Model\ValueObject\Object\GroupImage;
use Common\Domain\Model\ValueObject\String\Description;
use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\String\Name;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\Ports\FileUpload\FileInterface;
use Common\Domain\Security\UserShared;
use Common\Domain\Service\ServiceInputDtoInterface;
use Common\Domain\Validation\ValidationInterface;

class GroupModifyInputDto implements ServiceInputDtoInterface
{
    public readonly UserShared $userSession;
    public readonly Identifier $groupId;
    public readonly Name $name;
    public readonly Description $description;
    public readonly bool $imageRemove;
    public readonly GroupImage $image;

    public function __construct(UserShared $userSession, string|null $groupId, string|null $name, string|null $description, bool $imageRemove, FileInterface|null $image)
    {
        $this->userSession = $userSession;
        $this->groupId = ValueObjectFactory::createIdentifier($groupId);
        $this->name = ValueObjectFactory::createName($name);
        $this->description = ValueObjectFactory::createDescription($description);
        $this->imageRemove = $imageRemove;
        $this->image = ValueObjectFactory::createGroupImage($image);
    }

    public function validate(ValidationInterface $validator): array
    {
        return $validator->validateValueObjectArray([
            'group_id' => $this->groupId,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
        ]);
    }
}
