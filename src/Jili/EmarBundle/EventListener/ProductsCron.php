<?php
namespace Jili\EmarBundle\EventListener;

use Jili\EmarBundle\Entity\EmarProductsCron;

class ProductsCron extends BaseCron {

    private $web_and_cat_service;

    public function __construct() {
        $this->class_name_croned = 'JiliEmarBundle:EmarProductsCroned';
        $this->class_name_cron = 'JiliEmarBundle:EmarProductsCron';
    }

    /**
     * insert new data
     */
    function save($products) {

        foreach($products as $pdt) {
            $em = $this->em;
            $logger = $this->logger;

            $emarProduct = $em->getRepository($this->class_name_cron )->findOneByPid($pdt['pid']);

            if( ! $emarProduct ) {
                $emarProduct = new EmarProductsCron;
                $emarProduct->setPid($pdt['pid']);

            }else {
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

            // insert/update the website_category_filter
            $this->web_and_cat_service->add($pdt['web_id'], $pdt['catid'] );

        }
    }

    public function setWebsiteAndCategory( $web_and_cat_service ) {
        $this->web_and_cat_service = $web_and_cat_service ;
    }
}
