<?php
namespace Jili\ApiBundle\EventListener\Cache;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder\SplFileInfo;

/**
 * Store the api fetched data into cache data
 **/
class FileHandler
{

    private $logger ;
    protected $data_path;

    public function __construct($data_path)
    {
        $this->data_path = $data_path;
    }

    /**
     * check the file attribute with validate duration config
     */
    public function isValid($key, $duration = 3600)
    {

        $result = true;
        $cached = $this->getFileName( $key);
        // if file not exists , invalid
        if( ! file_exists($cached) ) {
             $result = false;
        }

        if( true === $result && ! is_readable($cached)) {
            $result = false;
        }

        if( true === $result  ) {
            // now - file make time  > $duration , invalid
            $mtime = filemtime($cached);
            $ctime = filectime($cached);
            $last_time = ($mtime < $ctime) ? $ctime: $mtime;
            $now = time();

            if(  $now > $last_time  + $duration) {
                $result = false;
            }
        }

        return $result ;
    }
    /**
     * @param: $key the file name
     * @param: $value the content to store
     */
    public function set($key ,$value)
    {
        if(  empty($value)) {
            return false;
        }

        // save the $value,into $key, overwrite.
        $cached = $this->getFileName($key);

        $fs = new Filesystem();
        $dir = dirname($cached);
        if( !  $fs->exists( $dir) ){
            $fs->mkdir($dir);
        }
        @file_put_contents( $cached, serialize($value) , LOCK_EX);
    }
    /**
     *@param: $key the file name
     */
    public function remove($key)
    {
        // remove the file.
        $fs = new Filesystem();
        $cached = $this->getFileName( $key);
        if( $fs->exists( $cached) ){
            $fs->remove($cached);
        }
    }
    /**
     * @param: $key the file name to read from
     * is isValid() before get()
     */
    public function get($key)
    {
        $cached =  $this->getFileName($key);
        $fs = new Filesystem();

        if(!  $fs->exists( $cached) ){
            return array();
        }

        $prod_categories = @unserialize(file_get_contents($cached));

        return $prod_categories;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger  = $logger;
    }

    private function getFileName($key)
    {
        return  $this->data_path . '/'. $key. '.cached';
    }
}
