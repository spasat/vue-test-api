<?php


namespace App\EventListener;


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
        $data = [
            'message' => $_ENV['APP_ENV'] === 'dev' ? $exception->getMessage() : 'Server error!'
        ];

        if ($exception instanceof HttpExceptionInterface) {
            $jsonResponse->setStatusCode($exception->getStatusCode());
            $jsonResponse->headers->replace($exception->getHeaders());
        }

        $data = array_merge(['code' => $jsonResponse->getStatusCode()], $data);
        $jsonResponse->setData($data);

        $event->setResponse($jsonResponse);
    }
}