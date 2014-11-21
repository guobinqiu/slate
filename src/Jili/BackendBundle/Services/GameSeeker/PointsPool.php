<?php
namespace Jili\BackendBundle\Services\GameSeeker;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class PointsPool 
{

    private $em;
    private $points_strategy_file;
    private $logger;

    public function __construct($path) 
    {

        $this->logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'$path:') ). var_export( $path, true) );
        $this->points_strategy_file = $path;
    }
    // 
    /**
     * 发布 publish / refresh ? 
     * @param integer $created_at  timestamp game_seeker_points_pool.crated_at
     * return  
     */
    public function publish( $rules, $key ) {
        $em = $this->em;
        try {
            $this->logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'$path:') ). var_export( $path, true) );
            // back up current!
            $rules = $em->getRepository()->fetchToPublish();
            // wirte to the cache file.

            $this->points_strategy_file;
        } catch(\Exception $e ) {
            // restore current
        }

        // is_valid = 1 && created_at  = $edition
        // update the game_seeker_points_strategy_$edition.php ? .yml
        // file read lock !
        // write cache file
        // serialize the points & write into cache file.
        // return the points strategy list
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
