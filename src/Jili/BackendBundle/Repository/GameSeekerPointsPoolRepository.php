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
        // (0,0)
        $rules = str_replace(array("\r\n", "\r"), "\n", $rules);
        $b = explode("\n", $rules);
        $createdAt = new \Datetime();
        $em = $this->getEntityManager();
        try{
            $em->getConnection()->beginTransaction();
            $q = $em->createQuery('delete from Jili\BackendBundle\Entity\GameSeekerPointsPool m where m.isValid = 0 and m.isPublished = 0');
            $q->execute();
            foreach($b as $k => $v) {
                $columns = explode(':' , $v);
                $entity = new GameSeekerPointsPool();
                $entity->setCreatedAt($createdAt);
                $entity->setUpdatedAt($createdAt);
                $entity->setPoints($columns[1]);
                $entity->setSendFrequency( $columns[0]);
                $em->persist($entity);
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

            $q_update = $em->createQuery('update Jili\BackendBundle\Entity\GameSeekerPointsPool m set m.isValid = 1, m.isPublished=1 , m.PublishedAt = :now  where m.isValid =0 and m.isPublished = 0');
            $q_update->setParameter('now', new \DateTime() );

            $num_updated = $q_update->execute();
            $em->getConnection()->commit();
            return $num_updated;
        } catch( \Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    public function fetchToPublish()
    {
        return $this->findBy(array('isValid'=>0, 'isPublished'=>0 ) ); 
    }

    /** 
     * @abstract 返回是发布状态的积份方案.
     * @return array array( array(10000, 1), ... , array(1, 500));
     */
    public function fetchPublished( )
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.points,a.sendFrequency')
            ->where('a.isValid = 1')
            ->AndWhere('a.isPublished=1')
            ->getQuery();

        $points_strategy = array();
        foreach( $query->getResult() as $key => $row) {
            $points_strategy[] = array( $row['sendFrequency'], $row['points']);
        };
        return $points_strategy;
    }

    public function  batchSetPublished()
    {
        // remove old records by update {(is_valid , is_published)|(1, 1)}  to (0,1);
        //  update (0,0) to ( 1, 1) 
        $em = $this->getEntityManager();
        try{
            $em->getConnection()->beginTransaction();
            $q = $em->createQuery('update Jili\BackendBundle\Entity\GameSeekerPointsPool m where m.isValid = 0, m.isPublished = 1 where m.isValid = 1 and m.isPublished = 1');

            $q->execute();
            $q_update = $em->createQuery('update Jili\BackendBundle\Entity\GameSeekerPointsPool m set m.isValid = 1, m.isPublished=1 , m.publishedAt = :now  where m.isValid =0 and m.isPublished = 0');

            $q_update ->setParameter('now', new \DateTime());

            $num_updated = $q_update->execute();
            $em->getConnection()->commit();

            return $num_updated;
        } catch( \Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }


    public function fetchToEnable( )
    {
        return $this->findBy(array(
            'isValid' => 0,
            'isPublished' => 0));
    }

}
