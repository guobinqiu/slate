<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

use Jili\EmarBundle\Entity\EmarApiReturn;

/**
 *
 **/
class Logger
{
    private $em;

    private $logger;
    public function __construct(LoggerInterface $logger , EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     *
     * @param  $content the request uri of Emar
     *
     */
    public function log($content)
    {
        $logger = new EmarApiReturn();
        $logger->setContent( $content);
        $logger->setCreatedAt( date_create() );
        $this->em->persist($logger);
        return $this->em->flush();
    }

}
