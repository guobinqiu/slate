<?php

namespace Jili\BackendBundle\Services\MonthActivity;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\BackendBundle\Utility\JsonCacheFileHandler;

class  Gathering
{

    /**
     * @var array
     */
    protected $configs ;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    function __construct( $configs)
    {
        $this->configs = $configs;
    }

    public function getTotal()
    {
        $file = $this->configs ['taobao_order_total_src'] ;  
        $js = new JsonCacheFileHandler();
        $content =  $js->readCached($file);
        return $content['total'];
    }

    public function updateTotal($total)
    {
        return $this->createTotal($total);
    }

    public function createTotal($total)
    {
        $file = $this->configs ['taobao_order_total_src'] ;  
        $js = new JsonCacheFileHandler();
        $data = array(
            'total'=>$total
        );
        $js->writeCache($data, $file);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}

