<?php
namespace Jili\ApiBundle\Utility;

/**
 *加密算法
 **/
class FlowUtil {

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