<?php

declare(strict_types=1);

namespace Common\Adapter\Event\Exception;

use Common\Domain\Exception\DomainExceptionOutput;
use Common\Domain\Exception\DomainInternalErrorException;
use Common\Domain\Response\ResponseDto;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionEventSubscriber implements EventSubscriberInterface
{
    private const ERROR_404_MESSAGE = 'Not found: error 404';
    private const ERROR_403_MESSAGE = 'Access denied: error 403';
    private const ERROR_500_MESSAGE = 'Internal server error: error 500';

    private string $appEnv;

    public function __construct(string $appEnv)
    {
        $this->appEnv = $appEnv;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['__invoke']];
    }

    public function __invoke(ExceptionEvent $event)
    {
        if ('dev' === $this->appEnv) {
            return;
        }

        $exception = $event->getThrowable();
        $response = new ResponseDto();
        $status = Response::HTTP_OK;

        if ($exception instanceof NotFoundHttpException) {
            $response->setMessage(static::ERROR_404_MESSAGE);
            $status = Response::HTTP_NOT_FOUND;
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $response->setMessage(static::ERROR_403_MESSAGE);
            $status = Response::HTTP_FORBIDDEN;
        }

        if ($exception instanceof DomainInternalErrorException) {
            $response->setMessage(static::ERROR_500_MESSAGE);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if ($exception instanceof DomainExceptionOutput) {
            $response->setStatus($exception->getStatus());
            $response->setMessage($exception->getMessage());
            $response->setErrors($exception->getErrors());
            $status = $exception->getHttpStatus()->value;
        }

        $event->setResponse(new JsonResponse($response->toArray(), $status));
    }
}
