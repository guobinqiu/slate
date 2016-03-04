<?php
namespace Wenwen\AppBundle\WebService\Sop;

class SopUtil
{
   public static function getJsopURL($sop_params, $app_sop_host)
    {
        $required = array('app_id', 'app_mid', 'sig', 'time', 'sop_callback');

        if(count(array_intersect_key(array_flip($required), $sop_params)) !== count($required)) {
            throw new \Exception("Insuffucient parameter", 1);
        }

        $query = http_build_query(array(
                                    'app_id'       => $sop_params['app_id'],
                                    'app_mid'      => $sop_params['app_mid'],
                                    'sig'          => $sop_params['sig'],
                                    'time'         => $sop_params['time'],
                                    'sop_callback' => $sop_params['sop_callback'],
                                    ));
        $url = 'https://' . $app_sop_host . '/api/v1_1/surveys/js?' . $query;
        return $url;
    }
}
