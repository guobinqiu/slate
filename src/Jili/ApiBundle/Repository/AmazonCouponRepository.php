<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class AmazonCouponRepository extends EntityRepository
{
    public function getAmcoupon()
    {
        $query = $this->createQueryBuilder('ac');
        $query = $query->select('ac.id','ac.couponOd','ac.couponElec');
        $query = $query->Where('ac.userid is null');
        $query = $query->setFirstResult(0);
        $query = $query->setMaxResults(1);
        $query =  $query->getQuery();
        return $query->getResult();
    }

    public function countCoupon()
    {
        $query = $this->createQueryBuilder('ac');
        $query = $query->select('ac.id','ac.couponOd','ac.couponElec');
        $query = $query->Where('ac.userid is null');
        $query =  $query->getQuery();
        return count($query->getResult());
    }

    public function isUserCoupon($userid)
    {
        $query = $this->createQueryBuilder('ac');
        $query = $query->select('ac.id','ac.couponOd','ac.couponElec');
        $query = $query->Where('ac.userid = :userid');
        $query = $query->setParameter('userid',$userid);
        $query =  $query->getQuery();
        return count($query->getResult());
    }


}
