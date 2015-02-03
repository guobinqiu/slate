<?php
namespace Jili\BackendBundle\Utility;


use Symfony\Component\Filesystem\Filesystem;
/**
 * 
 **/
class PointsPool extends JsonCacheFileHandler
{
    
    protected $file; 
    protected $file_strategy;

    function __construct($file, $file_strategy, $is_daily = true)
    {
        $this->setDailyPointsPoolFile($file, $is_daily); 
        $this->file_strategy = $file_strategy;
    }

    public function fetchByRandom()
    {
       $pool = $this->isExists($this->file); 
       if( is_null($pool)) {
           $pointsStrategy = new PointsStrategy($this->file_strategy);
           $strategy = $pointsStrategy->get();
           
           unset($pointsStrategy);
           if(is_null($strategy)) {
               throw new \Exception('points strategy is not exists');
           }

           $this->build($strategy);
       }

       $pool  = $this->readCached($this->file);
       try {
           /// backup 
           if(0 === count($pool)) {
               return ;
           }
           $tmp = $this->backup($this->file);
           // read 
           $fp = fopen($this->file, 'r+');
           $c = 1;
           while(!flock($fp, LOCK_EX | LOCK_NB)) {
               $c++;
               sleep($c);
               if($c > 600){
                   fclose($fp);
                   throw new \Exception('Unable to obtain lock');
               }
           }
           // read all content
           $row = '';
           while (!feof($fp)) {
               $row .= fread($fp, 8192);
           }

           // the pool is empty
           if(empty($row)) {
               flock($fp, LOCK_UN);
               fclose($fp);
               throw  new \Exception('points pool cache is empty.');
           }
           $pool = json_decode($row, true);

           // 'json parse retruns empty';
           if( empty($pool)  ) {
               flock($fp, LOCK_UN);
               fclose($fp);
               return array();
           }

           $key = array_rand( $pool, 1);
           $value = $pool[$key];
           //echo strlen(json_encode($pool)),PHP_EOL;
           unset($pool[$key]);
           shuffle($pool);
           //echo strlen(json_encode($pool)),PHP_EOL;
           $r = ftruncate($fp, 0 );
           rewind($fp);
           if($r === false ) {
               flock($fp, LOCK_UN);
               fclose($fp);
               throw new \Exception('cannot truncate exception points pool cache');
           }
           fwrite( $fp, json_encode($pool));
           unset($pool);
           flock($fp, LOCK_UN);
           fclose($fp);
           // remove backup
           $fs = new Filesystem();
           $fs->remove($tmp);
           return  $value;
       } catch(\Exception $e) {
            //restore
            $this->restore($tmp, $file);
            throw $e;
        }
        return true;
    } 

    /**
     * @param array $data the points strategy
     */
    public function build( $points_strategy ) 
    {
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
            throw $e;
        }
        return $c;
    }


    /**
     * for development
     */
    public function cleanPointsPool()
    {
        $fs = new Filesystem();
        if($fs->exists($this->file)){
            $fs->remove($this->file);
        }
    }

    // return the daily points pool file name
    private function setDailyPointsPoolFile($file , $is_daily=true)
    {
        if( strpos($file,'YYYYmmdd') === false) {
            throw new \Exception($file . ' should includes YYYYmmdd');
        }

        if($is_daily  ) {
            $this->file = str_replace('YYYYmmdd', date('Ymd'),$file );
        } else {
            $this->file = str_replace('YYYYmmdd','',$file );
        }
        return $this;
    } 
}
