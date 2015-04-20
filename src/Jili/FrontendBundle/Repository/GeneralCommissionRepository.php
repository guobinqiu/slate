<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class GeneralCommissionRepository extends EntityRepository
{

    /**
     * @abstract 关联commission_data
     */
    public function findListByAdId($id) 
    {

        $entity_name = $this->getEntityName();
        $meta_data = $this->getClassMetadata();

        $qb = $this->createQueryBuilder('c');

        $query = $qb->select('a, c.createdAt')
            ->innerJoin($entity_name.'Data', 'a', 'WITH', 'c.id= a.commissionId')
            ->Where('c.id = :id')
            ->andWhere('c.isActivated = 1')
            ->setParameter('id',$id)
            ->getQuery();

        return $query->getResult();
    }


}

