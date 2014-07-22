<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class IsReadFileRepository extends EntityRepository
{
    public function fileByTime()
    {
        $query = $this->createQueryBuilder('irf');
        $query = $query->select('irf.createTime,irf.csvFileName');
        $query = $query->orderBy('irf.createTime','DESC');
        $query =  $query->getQuery();
        return $query->getResult();
    }

}
