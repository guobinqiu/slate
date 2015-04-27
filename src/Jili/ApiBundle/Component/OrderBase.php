<?php
namespace Jili\ApiBundle\Component;

/**
 * 状态的迁移1->2->3/4 是订单中的状态取值，也是 历史记录中的状态取值。
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
    public static function isCompleted($order)
    {
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

    public static function getStatusList()
    {
        return self::$CONFIG;
    }


    public static function getInitStatus() 
    {
        return self::$CONFIG['INIT'];
    }

    public static function getPendingStatus() 
    {
        return self::$CONFIG['Pending'];
    }


    public static function getSuccessStatus()
    {
        return self::$CONFIG['COMPLETED_SUCCEEDED'];
    }

    public static function getFailedStatus()
    {
        return self::$CONFIG['COMPLETED_FAILED'];
    }
}
