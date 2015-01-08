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
            ->setUser($em->getReference('Jili\\ApiBundle\\Entity\\User', $params['userId']));
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param array  $params array('userId'=>)
     */
    public function isChecked($params)
    {
        $em = $this->getEntityManager();
        $query =$em->createQuery('select count(l.id) from Jili\ApiBundle\Entity\ActivityGatheringCheckinLog l where l.user = :user');
        $query->setParameter('user', $em->getReference('Jili\\ApiBundle\\Entity\\User', $params['userId']));

        $count = $query->getSingleScalarResult();
        if( isset($count) && $count >=  1 ) {
            return true;
        }
        return false;
    }
}
