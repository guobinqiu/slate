<?php
namespace Jili\EmarBundle\EventListener\Cache;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * 
 **/
class EmarResponse 
{

    private $emar_request;
    private $cache_config;

    public function __construct( $cache_config)
    {
         $this->cache_config = $cache_config;
    }
   

    /***
     * the cache key is based on request api & params
     */
    public function getKey(){
        $req = $this->emar_request;
        return md5( $req->getApiMethodName(). json_encode( $req->getApiParams() ) );
    }

    /***
     * @return: the life time of cache
     */
    public function getDuration() {
        $req = $this->emar_request;
        $duration = 0;
        $api_name = $req->getApiMethodName() ;
        $api_params = $req->getApiParams();
        $config = $this->cache_config;
        if(  array_key_exists( $api_name , $config)) {
            foreach( $config[$api_name ] as  $key => $value ) {
                if( isset( $api_params[$key]) && isset($value[ $api_params[$key]])   ) {
                    $duration = $value[ $api_params[ $key] ];
                    break;
                }
            }
            if( 0 ===  $duration ) {
                $duration = $config[ $api_name ]['default'];
            }
        }

        return $duration;
    }


    public function setEmarRequest($emar_request)
    {
       $this->emar_request  = $emar_request; 
       return $this;
    }
    
    public function isValid($key , $duration) {
        return $this->file_handler->isValid($key, $duration);
    }
    public function set($key, $data){
        return $this->file_handler->set($key, $data);
    }
    public function get($key) {
        return $this->file_handler->get( $key);
    }

    public function remove($key) {
        return $this->file_handler->remove( $key);
    }

    /**
     * use the file to cache data
     */
    public function setCacheHandler($file_hanlder) {
        $this->file_handler = $file_hanlder;
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }
}
