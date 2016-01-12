<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\UserWenwenLogin;

class UserWenwenLoginRepository extends EntityRepository
{
    public function createOne($params ) 
    {
        $em = $this->getEntityManager();
        $userWenwenLogin = new UserWenwenLogin();

        $userWenwenLogin->setUser( $em->getReference('Jili\\ApiBundle\\Entity\\User', $params['user_id'] ))
            ->setLoginPassword($params['password'])
            ->setLoginPasswordCryptType($params['crypt_type'])
            ->setLoginPasswordSalt($params['salt']);

        $em->persist($userWenwenLogin);
        $em->flush();
    } 

}
