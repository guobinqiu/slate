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
#         $logger->debug('{jarod}'. __FILE__.':'. __LINE__. ':'.var_export( $percentage, true) );
        
        $white_category = $this->getParameter('rebate_activity_category');
#         $logger->debug('{jarod}'. __FILE__.':'. __LINE__. ':'.var_export( $white_category, true) );

        if(is_array($white_category) && count($white_category) > 0 &&  in_array( $category, $white_category) ) {
            $new_point = round( $point * $percentage, 0);
        } else {
            $new_point = $point;
        }

#         $logger->debug('{jarod}'. __FILE__.':'. __LINE__.':' .var_export($point, true). '->'.var_export( $new_point, true) );
        return $new_point;
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }
}
