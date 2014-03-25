<?php
namespace Jili\EmarBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\EmarBundle\Entity\EmarProductsCroned;


class ProductStorage  {
    private $logger;
    private $em;

    /**
     * insert new data
     */
    function save($products) {

        foreach($products as $pdt) {
            $em = $this->em;
            $logger = $this->logger;

            $emarProduct = $em->getRepository('JiliEmarBundle:EmarProductsCroned')->findOneByPid($pdt['pid']);

            if( ! $emarProduct ) {
                $emarProduct = new EmarProductsCroned;
                $emarProduct->setPid($pdt['pid']);

            }else {
                $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). var_export($emarProduct, true) );
            }

            $emarProduct->setPName($pdt['p_name']);
            $emarProduct->setWebId($pdt['web_id']);
            $emarProduct->setWebName($pdt['web_name']);
            $emarProduct->setOriPrice($pdt['ori_price']);
            $emarProduct->setCurPrice($pdt['cur_price']);
            $emarProduct->setPicUrl($pdt['pic_url']);
            $emarProduct->setCatid($pdt['catid']);
            $emarProduct->setCname($pdt['cname']);
            $emarProduct->setPOUrl($pdt['p_o_url']);
            $emarProduct->setShortIntro($pdt['short_intro']);
            $em->persist($emarProduct);

            $em->flush();
        }
    }

    /**
     * remove old data/ 1 day
     */
    function remove($products  = array() , $duration =  86400 ) {
        $this->logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ) );
        $em = $this->em;
        $numDeleted = 0 ;
        if( count($products) >  0 ) {

        } else {
            $this->logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ) );

            $q = $em->createQuery('delete from Jili\EmarBundle\Entity\EmarProductsCroned p ');
            $numDeleted = $q->execute();
        }

        return $numDeleted;
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }
}
