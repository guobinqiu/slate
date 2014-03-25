<?php
namespace Jili\EmarBundle\Api2\Utils;

/**
 * 流量调用限制：20次/分钟
 * 应用状态： 开发测试
 * 
 **/
class PerRestrict
{
    
    private $storage;

    private $threadhold;// 
    function __construct()
    {
        $this->storage = array_fill( 0, 20, 0);

        //echo implode(',',$a),PHP_EOL;

        $this->threadhold = 60; // seconds
        $this->restrict = 20;// request per min

    }

    function add(){
        $t = time();
        array_unshift($this->storage, $t);
        $t_19 = array_pop($this->storage);
        //echo '  storage(after): ', implode(',',$this->storage),':', count($this->storage),PHP_EOL ;
        $diff =  $this->threadhold - $t + $t_19;;
        if($diff > 0) {
            echo '//  sleep: ' , $diff , 'seconds',PHP_EOL;
            sleep( $diff );
        }
        return $diff;
    }

    function getStorage() {
        return $this->storage;
    }
}
