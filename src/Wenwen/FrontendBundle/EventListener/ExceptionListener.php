<?php

namespace Wenwen\FrontendBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * 捕获Controller异常
 */
class ExceptionListener
{
    private $logger;
    private $templating;

    /**
     * @var KernelInterface
     *
     * $event->getKernel() returns an object which implements HttpKernelInterface (NOT KernelInterface)
     */
    private $kernel;

    public function __construct(LoggerInterface $logger,
                                EngineInterface $templating,
                                KernelInterface $kernel) {
        $this->logger = $logger;
        $this->templating = $templating;
        $this->kernel = $kernel;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof NotFoundHttpException) {
            $this->logger->error(__METHOD__ . ' Something wrong: ' . $exception);
        } else {
            $this->logger->warn(__METHOD__ . ' NotFoundHttpException: ' . $exception);
            $event->setResponse($this->templating->renderResponse('WenwenFrontendBundle:Error:error404.html.twig', array('errorMessage' => $exception->getMessage())));
        }

    }

//    public function onKernelRequest(GetResponseEvent $event)
//    {
//    }

//    public function onKernelView(GetResponseForControllerResultEvent $event)
//    {
//    }
}