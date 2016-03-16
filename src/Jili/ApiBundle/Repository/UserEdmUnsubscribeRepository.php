<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\UserEdmUnsubscribe;

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

    public function insertOne( $user_id) 
    {
        if(! is_int($user_id) || $user_id <= 0) {
            return;
        }

        $em = $this->getEntityManager();
        $userEdmUnsubscribe = $this->findOneBy(array('userId'=>$user_id));
        if( $userEdmUnsubscribe ) {
            return;
        }

        $userEdmUnsubscribe = new UserEdmUnsubscribe();
        $userEdmUnsubscribe->setUserId($user_id);
        $userEdmUnsubscribe->setCreatedTime(date_create());
        $em->persist($userEdmUnsubscribe);
        $em->flush();
    }
}
