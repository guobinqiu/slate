<?php
namespace Jili\ApiBundle\Services;

use Doctrine\ORM\EntityManager;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserBind
{
    private $em;
    /**
     * UserBind
     * @params $params array()
     */
    public function qq_user_bind(array $params)
    {
        if (isset($params['email']) && isset($params['open_id'])) {
            $user = $this->em->getRepository('JiliApiBundle:User')->findOneBy(array('email'=> $params['email']));
            if( $user) {
                echo 222;
                $params['user_id'] =  $user->getId();
                $qquser = $this->em->getRepository('JiliApiBundle:QQUser')->qquser_insert($params);
            } else {
                echo 111;
            }
        }
        return $this;
    }
    
    public function bind_vaildate(array $params)
    {
        if ($user->pw_encode($password) != $user->getPwd()) {
            $code = $this->getParameter('login_wr');
            return $code;
        }
    }
    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
    
    
}