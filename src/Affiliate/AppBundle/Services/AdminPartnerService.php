<?php

namespace Affiliate\AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Affiliate\AppBundle\Entity\AffiliatePartner;

/**
 * 
 */
class AdminPartnerService
{
    private $logger;

    private $em;

    private $knp_paginator;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                $knp_paginator)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->knp_paginator = $knp_paginator;
    }

    /**
    * 查找是否存在这个partnerId
    * @param $partnerId
    */
    public function validatePartnerStatus($partnerId){
        $this->logger->debug(__METHOD__ . " START partnerId=" . $partnerId . PHP_EOL);

        $rtn = array();

        $affiliatePartner = $this->em->getRepository('AffiliateAppBundle:AffiliatePartner')->findOneById($partnerId);
        if($affiliatePartner == null || sizeof($affiliatePartner) == 0){
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = 'PartnerId not exist. partnerId=' . $partnerId;
        } else {
            $rtn['status'] = 'success';
        }

        $this->logger->debug(__METHOD__ . " END   partnerId=" . $partnerId . PHP_EOL);
        return $rtn;
    }

    public function getPartnerList($page, $limit){
        $pagination = $this->em->getRepository('AffiliateAppBundle:AffiliatePartner')->getParnters($this->knp_paginator, $page, $limit);
        
        return $pagination;
    }

    public function addPartner($name, $description){
        $this->logger->debug(__METHOD__ . " START name=" . $name . PHP_EOL);

        $rtn = array();
        try{
            $affiliatePartner = new AffiliatePartner();
            $affiliatePartner->setName($name);
            $affiliatePartner->setDescription($description);
            $this->em->persist($affiliatePartner);
            $this->em->flush();
            $rtn['status'] = 'success';
        } catch (\Exception $e){
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = $e->getMessage();
        }

        $this->logger->debug(__METHOD__ . " END   name=" . $name . PHP_EOL);
        return $rtn;
    }

}