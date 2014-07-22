<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class IdentityConfirmRepository extends EntityRepository
{
    public function issetIndentity($identityCard,$userId)
    {
        $query = $this->createQueryBuilder('ic');
        $query = $query->select('ic.id,ic.userId');
        $query = $query->Where('ic.identityCard = :identityCard');
        $query = $query->andWhere('ic.userId <> :userId');
        $query = $query->setParameters(array('identityCard'=>$identityCard,'userId'=>$userId));
        $query =  $query->getQuery();
        return $query->getResult();
    }





}
