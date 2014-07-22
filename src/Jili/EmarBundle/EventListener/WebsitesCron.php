<?php
namespace Jili\EmarBundle\EventListener;

use Jili\EmarBundle\Entity\EmarWebsitesCron;

class WebsitesCron extends BaseCron
{
    public function __construct()
    {
        $this->class_name_croned = 'JiliEmarBundle:EmarWebsitesCroned';
        $this->class_name_cron = 'JiliEmarBundle:EmarWebsitesCron';
    }

    public function save($website)
    {
            $em = $this->em;
            $logger = $this->logger;

            $emarWebsite = $em->getRepository($this->class_name_cron)->findOneByWebId($website['web_id']);

            if( ! $emarWebsite ) {
                $emarWebsite = new EmarWebsitesCron;
                $emarWebsite->setWebId($website['web_id']);
            } else {
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
}
