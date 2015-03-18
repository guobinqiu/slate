<?php
namespace Jili\ApiBundle\Utility;

class CurlUtil {

    public static function curl($url, $post = '', $cookie = '', $cookiejar = '', $referer = '') {
        $tmpInfo = '';
        $cookiepath = getcwd() . './' . $cookiejar;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        if ($referer) {
            curl_setopt($curl, CURLOPT_REFERER, $referer);
        } else {
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        }
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        if ($cookiejar) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiepath);
        }
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));
        }
        curl_close($curl);
        return $tmpInfo;
    }
}