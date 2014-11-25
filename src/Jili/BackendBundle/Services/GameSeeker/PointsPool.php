<?php
namespace Jili\BackendBundle\Services\GameSeeker;

class PointsPool 
{

    private $em;
    private $points_strategy_file;

    public function __construct($path) 
    {
        $this->points_strategy_file = $path;
    }
    // 
    /**
     * 发布
     * @param integer $created_at  timestamp game_seeker_points_pool.crated_at
     * return  
     */
    public function publish( $created_at) {
        $em = $this->em;
        $pointsStrategy = $em->getRepository('JiliBackendBundle:GameSeekerPointsPool')->fetchToPublish($created_at);

        // is_valid = 1 && created_at  = $edition
        // update the game_seeker_points_strategy_$edition.php ? .yml


         $this->points_strategy_file;
        // file read lock !
        // write cache file
        
// serialize the points & write into cache file.
        
        // return the points strategy list
    }

    // 某次寻宝积分生成
    public function generateRandomPoints() {

// read the 
        
    
    }

    // 验证寻宝积分是否有效。
    public function verifyPoints($points) {
        // checkin pool of $points  is not empty
    }

}
