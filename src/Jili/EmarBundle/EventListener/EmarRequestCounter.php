<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

/**
 *  emar_counter
 **/
class EmarRequestCounter
{
    private $logger;
    private $em ;

    public function increase( $tag ) {
        if( empty($tag)) {
            $tag  = date('YmdHi');
        }
        $em = $this->em;
        $row = $em->getRepository('JiliEmarBundle:EmarRequest')->findOneByTag($tag);
        if( ! $row) {
            $row = new \Jili\EmarBundle\Entity\EmarRequest;
            $row->setCount(1);
            $row->setTag($tag);
            $em->persist($row);
        } else {
            $row->setCount( $row->getCount()  + 1);
        }
        $em->flush();
    } 

    public function setEntityManager(  EntityManager $em) {
        $this->em= $em;
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }
}

