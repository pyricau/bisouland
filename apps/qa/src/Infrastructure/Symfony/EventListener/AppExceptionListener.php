<?php

declare(strict_types=1);

namespace Bl\Qa\Infrastructure\Symfony\EventListener;

use Bl\Qa\Domain\Exception\AppException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsEventListener]
final class AppExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        [$message, $statusCode] = match (true) {
            $exception instanceof AppException => [$exception->getMessage(), $exception->getCode()],
            $exception instanceof HttpException => [$exception->getMessage(), $exception->getStatusCode()],
            default => ['An unexpected error occurred', Response::HTTP_INTERNAL_SERVER_ERROR],
        };

        $event->setResponse(new JsonResponse(
            json_encode(['error' => $message], \JSON_THROW_ON_ERROR),
            $statusCode,
            json: true,
        ));
    }
}
