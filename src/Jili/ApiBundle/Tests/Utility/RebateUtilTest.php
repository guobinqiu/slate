<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\RebateUtil;

class RebateUtilTest extends \ PHPUnit_Framework_TestCase {

    public function testcalculateRebateAmount() {
        $cps_rebate_type = array (
            'point' => 1,
            'order' => 2,
            'sale' => 3
        );
        $rebate_point = 70;

        $value = array ();
        $value['rebateType'] = 1;
        $value['rebate'] = 0.1;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(7, $user_rebate);

        $value = array ();
        $value['rebateType'] = 2;
        $value['rebate'] = 0.2;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(14, $user_rebate);

        $value = array ();
        $value['rebateType'] = 3;
        $value['rebate'] = 30;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(21, $user_rebate);
    }
}