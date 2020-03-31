<?php


namespace App\EventListener;


use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class KernelExceptionListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $exception = $event->getThrowable();
        $isJsonRequest = $request->getContentType() === 'json';

        if (!$isJsonRequest) {
            return;
        }

        $jsonResponse = new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        $message = 'Server error!';

        if ($exception instanceof UniqueConstraintViolationException) {
            $jsonResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            $message = 'Entity already exist!';
        }

        if ($exception instanceof HttpExceptionInterface) {
            $jsonResponse->setStatusCode($exception->getStatusCode());
            $jsonResponse->headers->replace($exception->getHeaders());
            $message = $exception->getMessage();
        }

        $jsonResponse->setData(
            [
                'code' => $jsonResponse->getStatusCode(),
                'message' => $message
            ]
        );

        $event->setResponse($jsonResponse);
    }
}