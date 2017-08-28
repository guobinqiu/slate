<?php

namespace Wenwen\FrontendBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

/**
 * 捕获所有web controller和api controller层抛出的异常
 */
class ExceptionListener
{
    private $logger;
    private $templating;

    public function __construct(LoggerInterface $logger, EngineInterface $templating)
    {
        $this->logger = $logger;
        $this->templating = $templating;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logger->error(__METHOD__ . ' ' . $exception->getTraceAsString());

        $path = $event->getRequest()->getPathInfo();
        $this->logger->info(__METHOD__ . ' path=' . $path);

        if (preg_match('/^\/v\d+\//', $path)) {//api
            $response = new JsonResponse();
            $response->setData(ApiUtil::formatError($exception->getMessage()));
            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
            } else {
                $response->setStatusCode(HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
            }
            $event->setResponse($response);
        } else { //web
            $event->setResponse($this->templating->renderResponse('WenwenFrontendBundle:Error:error.html.twig', array('errorMessage' => $exception->getMessage())));
        }
    }
}