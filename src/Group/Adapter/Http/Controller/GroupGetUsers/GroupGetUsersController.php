<?php

declare(strict_types=1);

namespace Group\Adapter\Http\Controller\GroupGetUsers;

use Common\Adapter\Security\UserSharedSymfonyAdapter;
use Common\Domain\Config\AppConfig;
use Common\Domain\Response\RESPONSE_STATUS;
use Common\Domain\Response\ResponseDto;
use Group\Adapter\Http\Controller\GroupGetUsers\Dto\GroupGetUsersRequestDto;
use Group\Application\GroupGetUsers\Dto\GroupGetUsersInputDto;
use Group\Application\GroupGetUsers\Dto\GroupGetUsersOutputDto;
use Group\Application\GroupGetUsers\GroupGetUsersUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag('Group')]
#[OA\Get(
    description: 'Get groups users information',
    parameters: [
        new OA\Parameter(
            name: 'group_id',
            in: 'path',
            required: true,
            description: 'Group\'s id',
            example: '5483539d-52f7-4aa9-a91c-1aae11c3d17f',
            schema: new OA\Schema(type: 'string')
        ),
        new OA\Parameter(
            name: 'page',
            in: 'query',
            required: false,
            description: 'Number of users to skip before starting to return',
            example: 1,
            schema: new OA\Schema(type: 'int')
        ),

        new OA\Parameter(
            name: 'page_items',
            in: 'query',
            required: false,
            description: 'Maximum number of users returned. (max. users: '.AppConfig::ENDPOINT_GROUP_GET_USERS_MAX_USERS.')',
            example: 50,
            schema: new OA\Schema(type: 'int')
        ),
    ],
    responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Get users of the group',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'message', type: 'string', example: 'Users of the group'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'page', type: 'integer', example: '1', description: 'Number of the current page'),
                                new OA\Property(property: 'pages_total', type: 'integer', example: '5', description: 'Number of total pages'),
                                new OA\Property(property: 'users', type: 'array', items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'id', type: 'string', example: '1fcab788-0def-4e56-b441-935361678da9'),
                                        new OA\Property(property: 'name', type: 'string', example: 'UserName'),
                                        new OA\Property(property: 'admin', type: 'boolean'),
                                        new OA\Property(property: 'image', type: 'string', example: 'User\'s image'),
                                    ])),
                            ])),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items()),
                    ]
                )
            )
        ),
        new OA\Response(
            response: Response::HTTP_NOT_FOUND,
            description: 'Group not found',
        ),
        new OA\Response(
            response: Response::HTTP_BAD_REQUEST,
            description: 'Could not find group or users',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'message', type: 'string', example: 'Some error message'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items()),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(default: '<groups_id|group_not_found|page|offset, string>')),
                    ]
                )
            )
        ),
        new OA\Response(
            response: Response::HTTP_UNAUTHORIZED,
            description: 'You not belong to the group',
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'message', type: 'string', example: 'You have not permissions'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items()),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(default: '<permissions, string>')),
                    ]
                )
            )
        ),
    ]
)]
class GroupGetUsersController extends AbstractController
{
    public function __construct(
        private Security $security,
        private GroupGetUsersUseCase $groupGetUsersUseCase
    ) {
    }

    public function __invoke(GroupGetUsersRequestDto $request): JsonResponse
    {
        $groupUsers = $this->groupGetUsersUseCase->__invoke(
            $this->createGroupGetUsersInputDto($request->groupId, $request->page, $request->pageItems)
        );

        return $this->createResponse($groupUsers);
    }

    private function createGroupGetUsersInputDto(string|null $groupId, int $page, int $pageItems): GroupGetUsersInputDto
    {
        /** @var UserSharedSymfonyAdapter $userAdapter */
        $userAdapter = $this->security->getUser();

        return new GroupGetUsersInputDto($userAdapter->getUser(), $groupId, $page, $pageItems);
    }

    private function createResponse(GroupGetUsersOutputDto $groupUsers): JsonResponse
    {
        $responseDto = (new ResponseDto())
            ->setMessage('Users of the group')
            ->setStatus(RESPONSE_STATUS::OK)
            ->setData($groupUsers->toArray());

        return new JsonResponse($responseDto, Response::HTTP_OK);
    }
}
