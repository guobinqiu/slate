<?php
namespace Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\FrontendBundle\Entity\UserVisitLog;

class UserVisitLogRepository extends EntityRepository
{
    /**
     * @param array $params array(userId=> ,
     */
    function logGameSeeker($params) 
    {
        $entity = new UserVisitLog();
        $entity->setUserId($params['userId'])
            ->setTargetFlag( UserVisitLog::TARGET_FLAG_GAME_SEEKER);
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush($entity);
        return $entity;
    } 

    /**
     * @return integer 0: no record found, 1: found
     */
    function isGameSeekerDoneDaily( $userId)
    {
        $today = new \DateTime();
        $today->setTime(0,0);
        $entity = $this->findOneBy(array(
            'userId'=> $userId,
            'visitDate'=>$today
        ));

        return  is_null($entity) ? 0: 1;

    }
}
