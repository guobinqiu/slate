<?php
namespace Jili\ApiBundle\EventListener;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

/**
 * @abstract 返利活动,　提高返还的积分
 */
class RebateActivity
{

    private $em;
    private $logger;

    public function __construct(LoggerInterface $logger, EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     *
     * @params $point number  the raw point
     * @params $category advertiserment category
     * @return $point * max( activity.percentage)
     */
    public function calcPointByCategory( $point , $category , \Datetime $at = null ) {

        $em = $this->em;

        $logger = $this->logger;
        $percentage = (float) $em->getRepository('JiliApiBundle:AdActivity')->findMaxPercentage( $at );

        $white_category = $this->getParameter('rebate_activity_category');

        if(is_array($white_category) && count($white_category) > 0 &&  in_array( $category, $white_category) ) {
            $new_point = round( $point * $percentage, 0);
        } else {
            $new_point = $point;
        }

        return $new_point;
    }

    /**
     *
     * @return rebate
     */
    public function getRebate($category = null) {
        if ($category === 'emar') {
            return $this->getParameter('emar_com.cps.action.default_rebate');
        }
        return $this->getParameter('cps_default_rebate');
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }
}
