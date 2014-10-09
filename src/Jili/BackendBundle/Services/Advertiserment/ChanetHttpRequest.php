<?php
namespace Jili\BackendBundle\Services\Advertiserment;


class ChanetHttpRequest
{
    private $return;
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $url , the imageUrl
     */
    public function fetch() 
    {
        $url = $this->url;
        if( strlen($url) <= 0 ) {
            return '';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $return = curl_exec($ch);
        curl_close($ch);

        $this->return = $return;
        return true;
    }

    /**
     * @return 如果返回的是script，则解析其中的window.location.href； 或返回原始url
     * The response of curl request must match pattern "^<script>window.location.href='http://%s';</script>$"
     */
    public function getDestinationUrl()
    {
        $return  = $this->getRawReturn();
        $url = $this->url;
        $c = trim($return);
        if(!empty($c) &&  substr($c, -9 )  === '</script>' && substr($c, 0,30 ) === '<script>window.location.href=\'' ) {
            return  substr($c, 30, -11); 
        } else {
            return $this->url;
        }
    } 


    /**
     * @abstract 返回原始的请求返回内容。
     */
    public function getRawReturn()
    {
        return $this->return;
    }

    /**
     * @abstract 有些广告已经过期的，返回的是一个静态的html.  比较其MD5 Hash.
     */
    public function isExpired()
    {
        return  ('c97eaac88c05d856b128d078477ba471' === md5($this->getRawReturn()) ) ? true : false;
    }
}
