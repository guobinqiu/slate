<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Bridge\Monolog\Logger;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\OfferwowApiReturn;

/**
 *
 **/
class OfferwowApiLogger
{

    private $em;

    private $logger;
    public function __construct(Logger $logger , $em)
    {

        $this->logger = $logger;
        $this->em = $em;

    //    //$this->em = $em;
    }

    /**
     *
     * @param  $content the request uri of Adw
     *
     */
    public function log($content)
    {
        $adwapi = new OfferwowApiReturn();
        $adwapi->setContent( $content);
        $adwapi->setCreatedAt( date_create() );
        $this->em->persist($adwapi);
        return $this->em->flush();
    }

}
