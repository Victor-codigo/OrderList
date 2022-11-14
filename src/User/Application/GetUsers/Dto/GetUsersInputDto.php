<?php

declare(strict_types=1);

namespace User\Application\GetUsers\Dto;

use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\Service\ServiceInputDtoInterface;
use Common\Domain\Validation\ValidationInterface;

class GetUsersInputDto implements ServiceInputDtoInterface
{
    /**
     * @var Identifier[]
     */
    public readonly array|null $usersId;

    public function __construct(array|null $usersId)
    {
        if (null === $usersId) {
            $this->usersId = null;

            return;
        }

        $this->usersId = array_map(
            fn (string $id) => ValueObjectFactory::createIdentifier($id),
            $usersId
        );
    }

    public function validate(ValidationInterface $validator): array
    {
        $errorList = $validator
            ->setValue($this->usersId)
            ->notNull()
            ->notBlank()
            ->validate();

        if (!empty($errorList)) {
            return $errorList;
        }

        return $validator->validateValueObjectArray($this->usersId);
    }
}
