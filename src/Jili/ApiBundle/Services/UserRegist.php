<?php
namespace Jili\ApiBundle\Services;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\User;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserRegist
{
    private $em;
        
    /**
     * UserRegiste
     * @params $params array()
     */
    public function qq_user_registe(array $params)
    {
        if (isset($params['email']) && isset($params['open_id']) && isset($params['pwd'])) {
            $user = $this->em->getRepository('JiliApiBundle:User')->qquser_quick_insert($params);
            $params['pwd'] = $user->pw_encode($params['pwd']);
            $params['user_id'] =  $user->getId();
            $qquser = $this->em->getRepository('JiliApiBundle:QQUser')->qquser_insert($params);
            var_dump($qquser);
        }
        return $qquser;
    }
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
}