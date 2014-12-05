<?php
namespace Jili/BackendBundle/Utility;

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
        $result = file_put_contents( $file, json_encode($data), LOCK_EX);
        if ( false === $result) {
            throw new \Exception('cannot write points strategy to cache_data');
//            $this->logger->crit('[gameSeeker][pointsPool][writeCache]cannot write points strategy to cache_data/' );
        }
        return $result;
    }

    // read the cache.
    protected function readCached($file)
    {
        $fs = new Filesystem();
        if(!  $fs->exists( $file) ){
            return ;
        }
        return @json_decode(file_get_contents($file), true);
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

