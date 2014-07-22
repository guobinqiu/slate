<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 *
 **/
class EmarWebsitesCategoryCronRepository extends EntityRepository
{

    public function count()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(p) FROM JiliEmarBundle:EmarWebsitesCategoryCron p')
            ->getSingleScalarResult();
    }

}
