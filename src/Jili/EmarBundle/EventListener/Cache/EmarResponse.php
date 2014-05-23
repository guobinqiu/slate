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


    private $key;
    private $cache;

    public function __construct( $cache_config)
    {
         $this->cache_config = $cache_config;
    }
   
    /***
     * the cache key is based on request api & params
     */
    public function getKey(){
        return  $this->key;
    }

    private function setKey(){
        $req = $this->emar_request;
        $this->key = $req->getApiMethodName().'.'. md5($req->getApiMethodName() . json_encode( $req->getApiParams() ) );
        return $this;
    }

    /***
     * @return: the life time of cache
     */
    public function getDuration() {
            return  $this->duration;
    }

    private function setDuration() {
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


        $this->duration = $duration;
        return $this;
    }

    public function setEmarRequest($emar_request)
    {
        $this->emar_request  = $emar_request; 
        $this->setKey();
        $this->setDuration();
        return $this;
    }
    
    public function isValid() {
        return $this->file_handler->isValid($this->getKey(), $this->getDuration());
    }
    public function set( $data){
        return $this->file_handler->set($this->getKey(), $data);
    }
    public function get() {
        return $this->file_handler->get( $this->getKey());
    }

    public function remove() {
        return $this->file_handler->remove( $this->getKey() );
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

