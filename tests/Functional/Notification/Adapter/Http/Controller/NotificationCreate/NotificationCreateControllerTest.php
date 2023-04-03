<?php

declare(strict_types=1);

namespace Test\Functional\Notification\Adapter\Http\Controller\NotificationCreate;

use Common\Domain\Response\RESPONSE_STATUS;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Notification\Domain\Model\NOTIFICATION_TYPE;
use Symfony\Component\HttpFoundation\Response;
use Test\Functional\WebClientTestCase;

class NotificationCreateControllerTest extends WebClientTestCase
{
    use RefreshDatabaseTrait;

    private const ENDPOINT = '/api/v1/notification';
    private const METHOD = 'POST';
    private const USER_ID = '1befdbe2-9c14-42f0-850f-63e061e33b8f';
    private const USER_2_ID = '2606508b-4516-45d6-93a6-c7cb416b7f3f';
    private const USER_3_ID = '6df60afd-f7c3-4c2c-b920-e265f266c560';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function itShouldCreateANotification(): void
    {
        $usersId = [self::USER_ID];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => NOTIFICATION_TYPE::USER_REGISTERED,
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, ['id'], [], Response::HTTP_CREATED);
        $this->assertSame(RESPONSE_STATUS::OK->value, $responseContent->status);
        $this->assertSame('Notification created', $responseContent->message);
        $this->assertCount(count($usersId), $responseContent->data->id);
    }

    /** @test */
    public function itShouldCreateManyNotification(): void
    {
        $usersId = [
            self::USER_ID,
            self::USER_2_ID,
            self::USER_3_ID,
        ];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => NOTIFICATION_TYPE::USER_REGISTERED,
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, ['id'], [], Response::HTTP_CREATED);
        $this->assertSame(RESPONSE_STATUS::OK->value, $responseContent->status);
        $this->assertSame('Notification created', $responseContent->message);
        $this->assertCount(count($usersId), $responseContent->data->id);
    }

    /** @test */
    public function itShouldFailCreatingAUserNotificationUserIdNotValid(): void
    {
        $usersId = [
            self::USER_ID,
            'not a valid user',
            self::USER_3_ID,
        ];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => NOTIFICATION_TYPE::USER_REGISTERED,
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, [], ['users_id'], Response::HTTP_BAD_REQUEST);
        $this->assertSame(RESPONSE_STATUS::ERROR->value, $responseContent->status);
        $this->assertSame('Error', $responseContent->message);
        $this->assertEquals(['uuid_invalid_characters'], $responseContent->errors->users_id);
    }

    /** @test */
    public function itShouldFailCreatingAUsersNotificationOneUserIdNotFound(): void
    {
        $usersId = [
            self::USER_ID,
            '22fd9f1f-ff4c-4f4a-abca-b0be7f965048',
            self::USER_3_ID,
        ];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => NOTIFICATION_TYPE::USER_REGISTERED,
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, [], ['users_wrong'], Response::HTTP_BAD_REQUEST);
        $this->assertSame(RESPONSE_STATUS::ERROR->value, $responseContent->status);
        $this->assertSame('Wrong users', $responseContent->message);
        $this->assertEquals('Wrong users', $responseContent->errors->users_wrong);
    }

    /** @test */
    public function itShouldFailCreatingAUsersNotificationNoUsersIdNotFound(): void
    {
        $usersId = [
            '22fd9f1f-ff4c-4f4a-abca-b0be7f965048',
        ];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => NOTIFICATION_TYPE::USER_REGISTERED,
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, [], ['users_wrong'], Response::HTTP_BAD_REQUEST);
        $this->assertSame(RESPONSE_STATUS::ERROR->value, $responseContent->status);
        $this->assertSame('Wrong users', $responseContent->message);
        $this->assertEquals('Wrong users', $responseContent->errors->users_wrong);
    }

    /** @test */
    public function itShouldFailCreatingAUsersNotificationNoUsersProvided(): void
    {
        $usersId = [];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => NOTIFICATION_TYPE::USER_REGISTERED,
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, [], ['users_id'], Response::HTTP_BAD_REQUEST);
        $this->assertSame(RESPONSE_STATUS::ERROR->value, $responseContent->status);
        $this->assertSame('Error', $responseContent->message);
        $this->assertEquals(['not_blank'], $responseContent->errors->users_id);
    }

    /** @test */
    public function itShouldFailCreatingAUserNotificationTypeWrong(): void
    {
        $usersId = [
            self::USER_ID,
            self::USER_2_ID,
            self::USER_3_ID,
        ];
        $client = $this->getNewClientAuthenticatedUser();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => 'wrong type',
            ])
        );

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertResponseStructureIsOk($response, [], ['type'], Response::HTTP_BAD_REQUEST);
        $this->assertSame(RESPONSE_STATUS::ERROR->value, $responseContent->status);
        $this->assertSame('Error', $responseContent->message);
        $this->assertEquals(['not_blank', 'not_null'], $responseContent->errors->type);
    }

    /** @test */
    public function itShouldFailCreatingAUserNotificationNotAuthorized(): void
    {
        $usersId = [
            self::USER_ID,
            self::USER_2_ID,
            self::USER_3_ID,
        ];
        $client = $this->getNewClientNoAuthenticated();
        $client->request(
            method: self::METHOD,
            uri: self::ENDPOINT,
            content: json_encode([
                'users_id' => $usersId,
                'type' => 'wrong type',
            ])
        );

        $response = $client->getResponse();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
    }
}
