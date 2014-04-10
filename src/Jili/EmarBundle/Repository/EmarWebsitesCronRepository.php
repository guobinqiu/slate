<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * for cron
 */
class EmarWebsitesCronRepository extends EntityRepository
{
    public function count() {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(p) FROM JiliEmarBundle:EmarWebsitesCron p')
            ->getSingleScalarResult();
    }


}
