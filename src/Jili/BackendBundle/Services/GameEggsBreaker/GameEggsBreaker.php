<?php
namespace Jili\BackendBundle\Services\GameEggsBreaker;
          ;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\BackendBundle\Utility\PointsStrategy;
use Jili\BackendBundle\Utility\PointsPool;

// write ranking to cache file
// read ranking 
class GameEggsBreaker 
{
    /**
     * @var array
     */
    protected $configs ;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    function __construct( $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @param string $rules
     * @param string $points_type common/consolation
     */
    public function publishPointsStrategy($rules, $points_type)
    {
        if(! isset( $this->configs[$points_type] ) )
        {
            throw new \Exception( 'points strategy path for ' .var_export($points_type, true). ' is not configured in '. var_export( $this->configs, true) );
        }
        $rules = str_replace(array("\r\n", "\r"), "\n", $rules);
        $rules_array  = explode("\n", $rules);
        $points_strategy = array();
        foreach( $rules_array  as $rule ) {
            list($frequnecy, $pts ) = explode(':' , $rule);
            $points_strategy[] = array( intval($frequnecy), intval($pts));
        }
        $file = $this->configs[$points_type]['points_strategy'];
//        if (count($points_strategy) === 0 ) {
//            throw new \Exception('No points strategy quried');
//        }
        $pointsStrategy = new PointsStrategy($file);
        return  $pointsStrategy->publish($points_strategy);
    }

    public function fetchRandomPoints($points_type)
    {
        if(! isset( $this->configs[$points_type] ) )
        {
            throw new \Exception( 'points strategy path for ' .var_export($points_type, true). ' is not configured in '. var_export( $this->configs, true) );
        }

        $pointsPool = new PointsPool( $this->configs[$points_type]['points_pool']
            ,$this->configs[$points_type]['points_strategy'] );
        // buildPointsPool
        return $pointsPool->fetchByRandom();
    }

//   public function updat
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
