<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class GeneralCommissionRepository extends EntityRepository
{

    /**
     * @abstract 关联commission_data
     */
    public function findListByAdId($ads_id) 
    {

        $entity_name = $this->getEntityName();
        $comm_data_entity_name  = $entity_name.'Data';
        
        $meta_data = $this->getClassMetadata();

        $qb = $this->createQueryBuilder('c');

        $query = $qb->select('a, c.createdAt')
            ->innerJoin($comm_data_entity_name, 'a', 'WITH', 'c.id = a.commissionId')
            ->Where('c.adsId = :ads_id')
            ->andWhere('c.isActivated = 1')
            ->setParameter('ads_id',$ads_id)
            ->getQuery();


        return $query->getResult();
    }


}

