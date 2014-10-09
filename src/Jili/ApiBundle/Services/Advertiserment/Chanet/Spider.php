<?php
namespace Jili\ApiBundle\Services\Advertiserment;

class Spider
{

    /**
     * @param string $url the advertierment.image_url
     * @return string the response html of $url 
     */
    public function fetch($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}
?>
