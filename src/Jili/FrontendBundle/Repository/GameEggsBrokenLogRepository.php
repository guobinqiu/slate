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
     * 取消 
     */
    public function getRanking()
    {
        // with group and sum order top 10
        // sum(points) 
        // group by user_id 
    }

}
