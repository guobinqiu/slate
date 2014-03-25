<?php
namespace Jili\EmarBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\EmarBundle\Entity\EmarWebsitesCroned;


class WebsiteStorage  {
    private $logger;
    private $em;

    /**
     * insert new data
     * ('web_id,web_name,web_catid,logo_url,web_url,information,begin_date,end_date,commission');
     *
     */
    function save($website) {

            $em = $this->em;
            $logger = $this->logger;

            $emarWebsite = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->findOneByWebId($website['web_id']);

            if( ! $emarWebsite ) {
                $emarWebsite = new EmarWebsitesCroned;
                $emarWebsite->setWebId($website['web_id']);
            } else {
                $logger->debug('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). var_export($emarWebsite, true) );
            }

            $emarWebsite->setWebName($website['web_name']);

            $emarWebsite->setWebCatid($website['web_catid']);
            $emarWebsite->setLogoUrl($website['logo_url']);
            $emarWebsite->setWebUrl($website['web_url']);

            $emarWebsite->setInformation($website['information']);

            $emarWebsite->setBeginDate($website['begin_date']);
            $emarWebsite->setEndDate($website['end_date']);
            $emarWebsite->setCommission($website['commission']);
            $em->persist($emarWebsite);

            $em->flush();
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
