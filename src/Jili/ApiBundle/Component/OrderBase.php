<?php
namespace Jili\ApiBundle\Component;

/**
 *
 */
class OrderBase
{
    private static $CONFIG = array(
        'INIT'=>  1 , 
        'PENDING'=> 2,
        'COMPLETED_SUCCEEDED'=> 3,
        'COMPLETED_FAILED'=> 4
    );

    /**
     * @param: $order 
     * @return:
     */
    static public function isCompleted($order) {
        $ret = false;
        if( method_exists($order,'getStatus') ) {
            //$logger->debug( $order, true );
            $ost = (int) $order->getStatus();
        } else if(method_exists($order,'getOrderStatus')) {
            $ost = (int) $order->getOrderStatus();
        } else {
            $ret = null;
        }

        if ( isset($ost) ) {
            if(self::$CONFIG['COMPLETED_SUCCEEDED'] ===  $ost || self::$CONFIG['COMPLETED_FAILED'] ===  $ost )  {
                $ret = true;
            }  else {
                $ret= false;
            }
        }
        return $ret;
    }

    static public function getStatusList() {
        return self::$CONFIG;
    }

    static public function getFailedStatus() {
        return self::$CONFIG['COMPLETED_FAILED'];
    }

    static public function getSuccessStatus() {
        return self::$CONFIG['COMPLETED_SUCCEEDED'];
    }
}


