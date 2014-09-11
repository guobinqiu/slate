<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;

class UserEdmUnsubscribeRepository extends EntityRepository {

    public function findByEmail($email = null) {
        $query = $this->createQueryBuilder('edm');
        $query = $query->select('edm.userId, edm.createdTime, u.email');
        $query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'edm.userId = u.id');
        if ($email) {
            $query = $query->Where('u.email = :email');
            $query = $query->setParameters(array (
                'email' => $email
            ));
        }
        $query = $query->getQuery();
        return $query->getResult();
    }
}