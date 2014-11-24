<?php
namespace Jili\BackendBundle\Services\GameSeeker;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PointsPool 
{

    private $em;
    private $config_paths;
    private $logger;

    /**
     * @param array $path  array(   
     *    'points_strategy' => 'app/cache_data/test/game_seeker_points_strategy_conf.json',
     *    'points_pool' => 'app/cache_data/test/game_seeker_points_pool_YYYYmmdd.json',
     *    'chest' => 'app/cache_data/test/game_seeker_config_chest.txt', ) 
     */
    public function __construct($path) 
    {
        $this->config_paths = $path;
    }

    // Write content with json_encode  
    public function writeCache($data, $file) 
    {
        $fs = new Filesystem();
        $dir = dirname($file);
        if( !  $fs->exists( $dir) ){
            $fs->mkdir($dir);
        }
        
        $fs->touch($file);
        if ( false === file_put_contents( $file, json_encode($data), LOCK_EX)) {
            $this->logger->crit('[game_seeker][writeCache]cannot write points strategy to cache_data/' );
        };
    }

    // read the cache.
    private function readCached($file)
    {
        $fs = new Filesystem();
        if(!  $fs->exists( $file) ){
            return ;
        }
        return @json_decode(file_get_contents($file));
    }

    // 从奖池中抽取宝奖分
    public function fetchByRandom()
    {
        $fh = fopen($path,'w');
        flock($fh, LOCK_EX);
        $data = fread($fh);
        $points_pool = json_decode($data, true);
        // add logic..
        fwrite($fh, $data); 
        flock($fh, LOCK_UN);
        fclose($fh);
    }

    
    /**
     * 发布 publish / refresh ? 
     * @param integer $created_at  timestamp game_seeker_points_pool.crated_at
     * return  
     *  update the game_seeker_points_strategy_$edition.php ? .yml
     *  file read lock !
     *  write cache file
     *  json_encode the points & write into cache file.
     *  return the points strategy list
     */
    public function publish() 
    {
        $em = $this->em;
        $fs = new Filesystem();
        try {
            $file = $this->config_paths['points_strategy'];
            if($fs->exists($file))  {
                $tmp = tempnam ( '/tmp/', 'jili_game_seeker_');
                $fs->copy($file , $tmp);
            }
            $rules = $em->getRepository('JiliBackendBundle:GameSeekerPointsPool')->fetchPublished();
            $this->writeCache( $rules, $file);
            if (isset($tmp) && $fs->exists($tmp)) {
                $fs->remove($tmp);
            }
            return $rules;
        } catch(\Exception $e ) {
            $this->logger->crit('[points_pool][publish]'. $e->getMessage());
            if (isset($tmp) && $fs->exists($tmp)) {
                $fs->copy( $tmp, $file);
                $fs->remove($tmp);
            }
            return $this->readCached();
        }
    }

    public function getPointsStrategyConfiguration() 
    {
        $path = $this->config_paths['points_strategy'];
        if(   $fs->readCache( $path) ){
            $this->publish();
        }
        return  ;
    }

    // 创建宝箱的奖分池, 每天生成
    public function build() 
    {
        // check config 
        
        // read the config 
    }

    // 某次寻宝积分生成
    public function generateRandomPoints() {

// read the pointPool daily 

    
    }

    // 验证寻宝积分是否有效。
    public function verifyPoints($points) {
        // checkin pool of $points  is not empty
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

}
