<?php
namespace Jili\BackendBundle\Utility;

use  Symfony\Component\Filesystem\Filesystem;

/**
 * 
 **/
class JsonCacheFileHandler 
{

    /**
     * Write content with json_encode  
     * @param array $data
     * @param string $file the file name 
     */
    public function writeCache($data, $file) 
    {
        $fs = new Filesystem();
        $dir = dirname($file);
        if( !  $fs->exists( $dir) ){
            $fs->mkdir($dir);
        }
        $fs->touch($file);
        // chmod() 700 
        $result = file_put_contents( $file, json_encode($data), LOCK_EX);
        if ( false === $result) {
            throw new \Exception('cannot write points strategy to cache_data');
//            $this->logger->crit('[gameSeeker][pointsPool][writeCache]cannot write points strategy to cache_data/' );
        }
        return $result;
    }

    public function fetch($file) 
    {

        $fp = fopen($file, 'w');
        $c = 1;
        while(!flock($fp, LOCK_EX | LOCK_NB)) {
            sleep(1);
            $c++;
            if ($c > 3) {
                throw new \Exception('busy request');
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    protected function isExists( $file)
    {
        $fs = new Filesystem();
        if(!  $fs->exists( $file) ){
            return ;
        }
        return false;
    }

    // read the cache.
    public function readCached($file)
    {
        if( is_null($this->isExists($file))) {
            return ;
        }
        return json_decode(file_get_contents($file), true);
    }

    protected function backup($file)
    {
        $fs = new Filesystem();

        if($fs->exists($file))  {
            $tmp = tempnam ( '/tmp/', 'jili_jsoncache_');
            $fs->copy($file , $tmp);
            return $tmp;
        }
    }

    protected function restore($tmp, $target) 
    {
        $fs = new Filesystem();
        if (isset($tmp) && $fs->exists($tmp)) {
            $fs->copy( $tmp, $target);
            $fs->remove($tmp);
        }
    }
}

