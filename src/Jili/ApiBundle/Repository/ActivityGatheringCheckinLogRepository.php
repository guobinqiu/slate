<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\ApiBundle\Entity\ActivityGatheringCheckinLog;
/**
 * 
 **/
class ActivityGatheringCheckinLogRepository extends EntityRepository
{
    /**
     * @param array  $params array('userId'=>)
     */
    public function log($params)
    {
        $em = $this->getEntityManager();
        $entity = new ActivityGatheringCheckinLog();

        $entity->setCheckinAt(new \DateTime())
            ->setUser($em->getReference('Jili\\ApiBundle\\User', $params['userId']));
        $em->persist($entity);
        $em->flush();
    }
    
}
