<?php

declare(strict_types=1);

namespace User\Domain\Service\EmailConfirmationJwtTokenValidationService;

use Common\Adapter\Jwt\Exception\JwtTokenExpiredException;
use Common\Domain\Exception\InvalidArgumentException;
use Common\Domain\Model\ValueObject\Array\Roles;
use Common\Domain\Model\ValueObject\Object\Rol;
use Common\Domain\Model\ValueObject\String\Identifier;
use Common\Domain\Model\ValueObject\String\JwtToken;
use Common\Domain\Model\ValueObject\ValueObjectFactory;
use Common\Domain\Ports\JwtToken\JwtHS256Interface;
use Common\Domain\Validation\User\USER_ROLES;
use User\Domain\Model\User;
use User\Domain\Port\Repository\UserRepositoryInterface;
use User\Domain\Service\EmailConfirmationJwtTokenValidationService\Dto\EmailConfirmationJwtTokenValidationDto;

class EmailConfirmationJwtTokenValidationService
{
    private JwtHS256Interface $jwt;
    private UserRepositoryInterface $userRepository;

    public function __construct(JwtHS256Interface $jwt, UserRepositoryInterface $userRepository)
    {
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws JwtTokenExpiredException
     * @throws InvalidArgumentException
     * @throws DBUniqueConstraintException
     * @throws DBNotFoundException
     * @throws DBConnectionException
     */
    public function __invoke(EmailConfirmationJwtTokenValidationDto $tokenDto): User
    {
        $tokenDecoded = $this->getToken($tokenDto->token);
        $userIdentifier = ValueObjectFactory::createIdentifier($tokenDecoded->username);
        $user = $this->getUser($userIdentifier);
        $this->setUserActive($user);

        return $user;
    }

    /**
     * @throws JwtTokenExpiredException
     */
    private function getToken(JwtToken $token): object
    {
        $tokenDecoded = $this->jwt->decode($token->getValue());

        if ($this->jwt->hasExpired($tokenDecoded)) {
            throw JwtTokenExpiredException::fromMessage('Token has expired');
        }

        return $tokenDecoded;
    }

    /**
     * @throws InvalidArgumentException
     * @throws DBNotFoundException
     */
    private function getUser(Identifier $userId): User
    {
        /** @var User $user */
        $user = $this->userRepository->findUserByIdOrFail($userId);

        if (!$user->getRoles()->has(new Rol(USER_ROLES::NOT_ACTIVE))) {
            throw InvalidArgumentException::fromMessage('User is already active');
        }

        return $user;
    }

    /**
     * @throws DBUniqueConstraintException
     * @throws DBConnectionException
     */
    private function setUserActive(User $user): void
    {
        $user->setRoles(Roles::create([USER_ROLES::USER_FIRST_LOGIN]));
        $this->userRepository->save($user);
    }
}
