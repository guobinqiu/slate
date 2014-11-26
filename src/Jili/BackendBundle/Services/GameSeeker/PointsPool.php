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

    const CHEST_COUNT = 5;
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
//        echo __FILE__, __LINE__,':',PHP_EOL;
 //       echo $file,PHP_EOL;
        $fs->touch($file);
        $result = file_put_contents( $file, json_encode($data), LOCK_EX);
        if ( false === $result) {
            $this->logger->crit('[gameSeeker][pointsPool][writeCache]cannot write points strategy to cache_data/' );
        };
        return $result;
    }

    // read the cache.
    private function readCached($file)
    {
        $fs = new Filesystem();
        if(!  $fs->exists( $file) ){
            return ;
        }
        return @json_decode(file_get_contents($file), true);
    }

    private function backup($file)
    {
        $fs = new Filesystem();
        if($fs->exists($file))  {
            $tmp = tempnam ( '/tmp/', 'jili_gameSeeker_');
            $fs->copy($file , $tmp);
            return $tmp;
        }
    }

    private function restore($tmp, $target) 
    {
        $fs = new Filesystem();
        if (isset($tmp) && $fs->exists($tmp)) {
            $fs->copy( $tmp, $target);
            $fs->remove($tmp);
        }
    }

    // return the daily points pool file name
    public function getDailyPointsPoolFile()
    {
        $file = $this->config_paths['points_pool'];
        $daily_file = str_replace('YYYYmmdd', date('Ymd'),$file );
        return $daily_file; 
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
            $rules = $em->getRepository('JiliBackendBundle:GameSeekerPointsPool')->fetchPublished();
            if (count($rules) === 0 ) {
                throw new \Exception('No points strategy quried');
            }

            $file = $this->config_paths['points_strategy'];
            $tmp = $this->backup($file);
            // write to cache and verify
            $this->writeCache( $rules, $file);
            $points_strategy = $this->readCached($file);
            if(! $fs->exists($file))  {
                throw new \Exception('write cache file failed');
            }
            
            if (isset($tmp) && $fs->exists($tmp)) {
                $fs->remove($tmp);
            }
            return $rules;
        } catch(\Exception $e ) {
            $this->logger->crit('[gameSeeker][pointsPool][publish]'. $e->getMessage());
            if( isset($tmp) ) {
                $this->restore($tmp, $file);
            }

            return array();
        }
    }

    // 从cache文件中返回 奖分方案.
    public function getPointsStrategyConfiguration() 
    {
        $path = $this->config_paths['points_strategy'];
        $points_strategy = $this->readCached( $path) ;

        if(  is_null($points_strategy)  ){
            $this->publish();
            $points_strategy =  $this->readCached( $path) ;
        }
        return  $points_strategy;
    }

    // 创建宝箱的奖分池, 每天生成
    public function build() 
    {
        // read the config 
        $points_strategy = $this->getPointsStrategyConfiguration();
        if( empty($points_strategy)) {
            $this->publish();
            $points_strategy = $this->getPointsStrategyConfiguration();
        } 
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
        $file = $this->getDailyPointsPoolFile();

        try {
            $this->writeCache( $c, $file);
        } catch( \Exception $e) {
            $this->logger->crit('[gameSeeker][pointsPool][build]'. $e->getMessage());
        }
    }

    // 某次寻宝积分生成, 从奖池中抽取宝奖分
    public function fetch() 
    {
        // read the pointPool daily 
        $file = $this->getDailyPointsPoolFile();
        $points_pool = $this->readCached($file);

        if( is_null($points_pool)) {
            $this->build();
            $points_pool = $this->readCached($file);
        } 

        $this->logger->debug('{jarod}'. implode(':', array(__FILE__, __LINE__,'count():')). var_export(count($points_pool), true) );
        $this->logger->debug('{jarod}'. implode(':', array(__FILE__, __LINE__,'is_array:')). var_export(is_array($points_pool), true) );
        $this->logger->debug('{jarod}'. implode(':', array(__FILE__, __LINE__,'is_null:')). var_export(is_null($points_pool), true) );

        if(0 === count($points_pool)) {
            return ;
        }
        try {
            /// backup 
            $tmp = $this->backup($file);
            $key = array_rand( $points_pool, 1);
            $value = $points_pool[$key];
            unset($points_pool[$key]);
            $this->writeCache( $points_pool, $file);
            return  $value;
        } catch(\Exception $e) {
            //restore
            $this->logger->crit('[gameSeeker][pointsPool][fetch]'. $e->getMessage());
            $this->restore($tmp, $file);
        }
        // fetch one
        // update 
        // save again.
        return true;
    }

    // 验证寻宝积分是否有效。
    public function verifyPoints($points) {
        // checkin pool of $points  is not empty
    }

    // 取每轮出现宝箱的个数
    public function fetchChestCount()
    {
        $file = $this->config_paths['chest'];
        $fs = new Filesystem();
        $chest_info = $this->readCached( $file );
        if(is_null($chest_info) ) {
            $this->writeCache(self::CHEST_COUNT, $file);

            $chest_info = $this->readCached( $file );
        }
        return $chest_info;
    }

    // Admin Clerk修改宝箱数
    public function updateChestCount( $quantity  ) 
    {
        $file = $this->config_paths['chest'];
        $fs = new Filesystem();
        return $this->writeCache($quantity, $file);
    }

//   public function update 
     //*    'chest' => 'app/cache_data/test/game_seeker_config_chest.txt', ) 
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
