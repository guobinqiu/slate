<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\UserSignUpRoute;

/**
 *
 **/
class UserSignUpRouteRepository extends EntityRepository
{
    /**
     * @param: $params = array('user_id'=> ,'source_route');
     */
    public function insert(array $params)
    {
        if ( isset($params['source_route']) && isset($params ['user_id'] ) ) {
            // new a log table
            $userSignUpRoute = new UserSignUpRoute();
            $userSignUpRoute->setUserId($params['user_id']);
            $userSignUpRoute->setSourceRoute($params['source_route']);
            $em = $this->getEntityManager();
            $em->persist($userSignUpRoute);
            $em->flush();
        }
        return $this;
    }
}
