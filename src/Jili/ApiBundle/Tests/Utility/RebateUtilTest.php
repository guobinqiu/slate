<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\RebateUtil;

class RebateUtilTest extends \ PHPUnit_Framework_TestCase
{
    public function testcalculateRebateAmount()
    {
        $cps_rebate_type = array (
            'point' => 1,
            'order' => 2,
            'sale' => 3
        );
        $rebate_point = 70;

        $value = array ();
        $value['rebateType'] = 1;
        $value['rebate'] = 10;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(700, $user_rebate);

        $value = array ();
        $value['rebateType'] = 2;
        $value['rebate'] = 10;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(700, $user_rebate);

        $value = array ();
        $value['rebateType'] = 3;
        $value['rebate'] = 10;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(7, $user_rebate);

        $value = array ();
        $value['rebateType'] = 3;
        $value['rebate'] = 0;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(0, $user_rebate);

        $value = array ();
        $value['rebateType'] = 3;
        $value['rebate'] = -1;
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals(0, $user_rebate);

        $value = array ();
        $value['rebateType'] = 3;
        $value['rebate'] = "re";
        $user_rebate = RebateUtil :: calculateRebateAmount($value, $cps_rebate_type, $rebate_point);
        $this->assertEquals("0", $user_rebate);
    }

    /**
     * @group issue_476
     */
    public function testcalculateRebate()
    {
        $reward_multiple = 3;
        $campaign_multiple = 2;
        $value = array('incentiveType'=>2,'incentiveRate'=>30,'rewardRate'=>30);
        $reward_rate = RebateUtil :: calculateRebate($reward_multiple, $campaign_multiple, $value);
        $this->assertEquals(0.27, $reward_rate);
        $reward_multiple = 0;
        $campaign_multiple = 2;
        $value = array('incentiveType'=>2,'incentiveRate'=>30,'rewardRate'=>30);
        $reward_rate = RebateUtil :: calculateRebate($reward_multiple, $campaign_multiple, $value);
        $this->assertEquals(0.18, $reward_rate);

    }
}
