<?php
namespace Jili\BackendBundle\Repository;
use Doctrine\ORM\EntityRepository;

use Jili\BackendBundle\Entity\GameSeekerPointsPool;
// use Symfony\Component\Filesystem\Filesystem;

class GameSeekerPointsPoolRepository extends EntityRepository 
{

    /**
     * @param String $a 
     *   points:frequency[:sequence1,sequence2,]
     *   points1:frequency2[:sequence1,sequence2,]
     *   ...
     * @return 
     */
    function batchInsertRules($rules) 
    {
        $b = explode(PHP_EOL, $rules);
        $batchSize = 20;
        $createdAt = new \Datetime();
        $em = $this->getEntityManager();
        foreach($b as $k => $v) {
            $columns = explode(':' , $v);
            $pool = new GameSeekerPointsPool();
            $pool->setCreatedAt($createdAt);
            $pool->setUpdatedAt($createdAt);
            $pool->setPoints($columns[1]);
            $pool->setSendFrequency( $columns[0]);
            $em->persist($pool);
        }
        $em->flush(); $em->clear(); // Detaches all objects from Doctrine!
    }

    public function fetchToEnable( $created_at)
    {
        $createdAt = new \DateTime();
        $createdAt ->setTimestamp($created_at);
        // latest createdAt
        $this->findBy(array(
            'isValid' => 0,
            'isPublished' => 0,
            'createdAt'=>$createdAt ));

    }

    public function fetchToPublish( $created_at)
    {
        
        $createdAt = new \DateTime();
        $createdAt ->setTimestamp($created_at);

        $this->findBy(array(
            'isValid' => 1,
            'isPublished' => 0,
            'createdAt'=>$createdAt ));

    }
}
