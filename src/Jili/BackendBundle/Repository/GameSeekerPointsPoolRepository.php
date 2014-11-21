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
        $createdAt = new \Datetime();
        $em = $this->getEntityManager();
        try{
            $em->getConnection()->beginTransaction();
            $q = $em->createQuery('delete from Jili\BackendBundle\Entity\GameSeekerPointsPool m where m.isValid = 0 and m.isPublished = 0');
            $q->execute();
            foreach($b as $k => $v) {
                $columns = explode(':' , $v);
                $pool = new GameSeekerPointsPool();
                $pool->setCreatedAt($createdAt);
                $pool->setUpdatedAt($createdAt);
                $pool->setPoints($columns[1]);
                $pool->setSendFrequency( $columns[0]);
                $em->persist($pool);
            }
            $em->flush(); 
            $em->clear(); // Detaches all objects from Doctrine!
            $em->getConnection()->commit();
        } catch( \Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }


    public function batchSetEnable( )
    {
        // delete ( 1, 0) 
        // move (0,0) to ( 1, 0) 
        $em = $this->getEntityManager();
        try{
            $em->getConnection()->beginTransaction();
            $q = $em->createQuery('delete from Jili\BackendBundle\Entity\GameSeekerPointsPool m where m.isValid = 1 and m.isPublished = 0');

            $q->execute();
            $q_update = $em->createQuery('update Jili\BackendBundle\Entity\GameSeekerPointsPool m set m.isValid = 1, m.isPublished=1 , m.PublishedAt = now()  where m.isValid =0 and isPublished = 0');

            $num_updated = $q_update->execute();
            $em->getConnection()->commit();
            return $num_updated;
        } catch( \Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    public function fetchToPublish( )
    {
        return $this->findBy(array(
            'isValid' => 0,
            'isPublished' => 0));
    }

    public function fetchToEnable( )
    {
        return $this->findBy(array(
            'isValid' => 0,
            'isPublished' => 0));
    }

}
