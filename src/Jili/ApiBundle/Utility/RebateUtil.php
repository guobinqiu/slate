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
    public static function calculateRebateAmount($value, $cps_rebate_type, $rebate_point) {
        if ($value['rebate'] <= 0) {
            return 0;
        }
        if ($value['rebateType'] == $cps_rebate_type['sale']) {
            return $value['rebate'] * ($rebate_point / 100);
        }
        if ($value['rebateType'] == $cps_rebate_type['point'] || $value['rebateType'] == $cps_rebate_type['order']) {
            return $value['rebate'] * $rebate_point;
        }
    }

    /**
     * @params: $reward_multiple
     * @params: $campaign_multiple
     * @params: $value
     * @return: reward_rate
     */
    public static function calculateRebate($reward_multiple, $campaign_multiple, $value) {
        if ($reward_multiple) {
            $reward_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
        } else {
            $reward_rate = $campaign_multiple;
        }
        $reward_rate = $value['incentiveRate'] * $value['rewardRate'] * $reward_rate;
        $reward_rate = round($reward_rate / 10000, 2);
        return $reward_rate;
    }
}