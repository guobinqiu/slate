<?php

namespace Jili/BackendBundle/Utility;

/**
 * 
 **/
class PointsPool extends JsonCacheFileHandler
{
    
    protected $file; 
    function __construct($file)
    {
        $this->setDailyPointsPoolFile($file); 
    }

    public function fetchByRandom()
    {
        $
    } 

    /**
     * @param array $data the points strategy
     */
    public function build( $points_strategy ) 
    {

        //$strategy = $this->writeCache($data, $this->file); 
        if(empty($points_strategy)) {
            return array();
        }

        $c = array();
        $key_begin = 0;
        foreach($points_strategy as $k => $v) {
            $p = $v[1];
            $f = $v[0];
            $tmp = array_fill( $key_begin , $f,$p);
            $key_begin = $key_begin + $f  - 1;
            $c = array_merge($c, $tmp);
        }
        shuffle($c);

        try {
            $this->writeCache( $c, $this->file);
        } catch( \Exception $e) {
            throw new \Exception();
//            $this->logger->crit('[gameEggsBreaker][pointsPool][build]'. $e->getMessage());
        }
    }

    // return the daily points pool file name
    private function setDailyPointsPoolFile($file)
    {
        if( strpos('YYYYmmdd', $file) === false) {
            throw new \Exception($file . ' should includes YYYYmmdd');
        }
        $this->file = str_replace('YYYYmmdd', date('Ymd'),$file );
        return $this;
    } 
}
