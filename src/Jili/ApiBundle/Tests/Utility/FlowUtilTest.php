<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\FlowUtil;

class FlowUtilTestTest extends \PHPUnit_Framework_TestCase {

    /**
     * @group issue_682
     */
    public function test_params_md5() {
        $params['a'] = 'a=test1';
        $params['b'] = 'b=test2';
        $secretkey = '123';

        $list_params = FlowUtil :: params_combine($params);
        $list_secrectparams = $list_params . $secretkey;
        $this->assertEquals(md5($list_secrectparams), FlowUtil :: params_md5($params, $secretkey));
    }

    /**
     * @group issue_682
     */
    public function test_params_combine() {
        $params['a'] = 'a=test1';
        $params['b'] = 'b=test2';

        sort($params);
        $list_params = $params[0];
        for ($i = 1; $i < count($params); $i++) {
            $list_params = $list_params . $params[$i];
        }
        $this->assertEquals($list_params, FlowUtil :: params_combine($params));
    }
}