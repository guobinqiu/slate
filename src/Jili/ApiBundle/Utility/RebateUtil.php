<?php
namespace Jili\ApiBundle\Utility;

/**
 *
 */
class RebateUtil {

    /**
     * @params: $value
     * @params: $cps_rebate_type
     * @params: $rebate_point
     * @return: user rebate
     */
    static public function calculateRebateAmount($value, $cps_rebate_type, $rebate_point) {
        if ($value['rebateType'] == $cps_rebate_type['sale']) {
            return $value['rebate'] * ($rebate_point / 100);
        }
        if ($value['rebateType'] == $cps_rebate_type['point'] || $value['rebateType'] == $cps_rebate_type['order']) {
            return $value['rebate'] * $rebate_point;
        }
    }
}