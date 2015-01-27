<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\FrontendBundle\Entity\GameEggsBrokenLog;

class GameEggsBrokenLogRepository extends EntityRepository 
{
    /**
     *  insert a new one
     */
    public function addLog($params)
    {
        $entity = new GameEggsBrokenLog();
        $entity->setUserId($params['userId']) 
            ->setEggType($params['eggType'])
            ->setPointsAcquried($params['points']);

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    /**
     * 最近一次成功砸蛋的时间
     */
    public function getLastestTimestampBroken()
    {
        $qb = $this->createQueryBuilder('l');
        $q = $qb->select($qb->expr()->max('l.createdAt') )
            ->where('l.pointsAcquried > 0')
            ->getQuery();
        return $q->getSingleScalarResult();
    }

    /**
     * 返回最近砸蛋的用户列表
     */
    public function findLatestBrokenNickList( $limit = 10)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sql = 'select a.user_id as userId, a.points_acquried as pointsAcquried, a.created_at as createdAt
            from game_eggs_broken_log a 
            inner join  
            ( select c.user_id , max(c.created_at) as last_at  from game_eggs_broken_log  c 
            where c.points_acquried > 0 
            group by c.user_id order by last_at  desc limit :limit offset 0   ) b 
            on ( b.user_id = a.user_id and b.last_at = a.created_at)
            order by a.created_at desc';
        $stat = $conn->prepare($sql);
        $stat->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stat->execute();
        $result =  $stat->fetchAll();

        if( empty($result) ) {
            return ;
        }

        $logs = array();
        $user_ids = array();
        foreach( $result as $k => $v ) {
           $user_ids [] = $v['userId'];
           $logs[$v['userId']] = $v;
        }
        unset($result);

        // select nick from user where id in (); 
        $qb = $em->createQueryBuilder();
        $q = $qb->select('u.id, u.nick')
            ->from('JiliApiBundle:User', 'u')
            ->where($qb->expr()->in('u.id', $user_ids))
            ->getQuery();
        $result = $q->getResult();
        $nicks = array();
        foreach($result as $k => $v) {
            $nicks[$v['id']] = $v['nick'];
        }
        unset($result);

        // merge the results
        $results = array();
        foreach( $logs as $user_id => $log) {
            $results [] = array(
                'nick'=> isset($nicks[$user_id]) ? $nicks[$user_id]: '',
                'pointsAcquried'=> isset($log['pointsAcquried']) ? $log['pointsAcquried']: 0,
                'at'=> $log['createdAt']
            ) ;
        }
        return $results; 

    }

}
