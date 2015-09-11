<?php
namespace Jili\ApiBundle\Utility;

class FlowUtil {

    // 流量包接口_手机号码验证接口
    public static $MOBILE_VALIDATE_ERROR = array (
        '200' => '成功',
        '201' => '缺少参数,custom_sn,mobile,enctext',
        '202' => 'custom_sn信息错误',
        '203' => '加密格式错误',
        '204' => '号码归属找不到',
        '206' => '手机号码不正确',
        '900' => '其他原因'
    );

    // 流量包接口_手机号码验证接口
    public static $CREATEORDER_API_ERROR = array (
        '101' => '成功',
        '201' => '缺少参数,custom_product_id,custom_sn,mobile,enctext',
        '202' => 'custom_sn错误',
        '203' => '加密格式错误',
        '204' => '号码归属找不到',
        '205' => '请求的产品不正确',
        '206' => '手机号码不正确',
        '207' => '订单号重复提交',
        '208' => '余额不足',
        '209' => '手机号码归属地和产品不匹配',
        '210' => '暂不支持此地区',
        '211' => 'custom_order_sn 超过30位字符',
        '212' => '请求的产品暂时关停',
        '900' => '其他原因'
    );

    /**
     *生成md5摘要
     **/
    public static function params_md5($params, $secretkey) {
        if ($secretkey === FALSE)
            return FALSE;
        $list_params = self :: params_combine($params);

        $list_secrectparams = $list_params . $secretkey;

        return md5($list_secrectparams);
    }

    /**
     *字典序升序算法
     **/
    public static function params_combine($params) {
        sort($params);

        $list_params = $params[0];
        for ($i = 1; $i < count($params); $i++) {
            $list_params = $list_params . $params[$i];
        }
        return $list_params;
    }
}
