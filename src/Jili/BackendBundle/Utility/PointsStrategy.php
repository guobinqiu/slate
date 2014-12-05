<?php
namespace Jili/BackendBundle/Utility;

/**
 * 
 **/
class PointsStrategy extends JsonCacheFileHandler
{

    protected $file; 
    function __construct($file)
    {
        $this->file  = $file;
    }

    public function publish($data)
    {
        return $this->writeCache( $data ,$this->file); 
    } 

    public function  get()
    {
        return $this->readCached($this->file);

    } 
}
