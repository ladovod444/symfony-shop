<?php

namespace App\EventSubscriber;

use App\Exceptions\ProductNotFound;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\DBAL\Exception\DriverException;
use Throwable;

class ExceptionResponseSubscriber implements EventSubscriberInterface
{
    
    private const EXCEPTION_RESPONSE_HTTP_CODE_MAP = [
        NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
        HttpException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
        DriverException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
        MethodNotAllowedHttpException::class => Response::HTTP_METHOD_NOT_ALLOWED,
        ProductNotFound::class => Response::HTTP_NOT_FOUND,
    ];

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $response = new JsonResponse(
            [
                'error' => $throwable->getMessage(),
            ],
            $this->httpCode($throwable),
            [
                'Content-Type' => 'application/json',
            ]
        );

        $event->setResponse($response);
    }

    private function httpCode(Throwable $throwable): int
    {
        $throwableClass = $throwable::class;
        if (array_key_exists($throwableClass, self:: EXCEPTION_RESPONSE_HTTP_CODE_MAP)) {
            return self:: EXCEPTION_RESPONSE_HTTP_CODE_MAP[$throwableClass];
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

}
