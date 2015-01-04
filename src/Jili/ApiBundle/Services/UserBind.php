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
     * qq UserBind
     * @params $params array()
     */
    public function qq_user_bind(array $params)
    {
        $qquser = null;
        if (isset($params['email']) && isset($params['open_id'])) {
            $user = $this->em->getRepository('JiliApiBundle:User')->findOneBy(array('email'=> $params['email']));
            if( $user) {
                $params['user_id'] =  $user->getId();
                $qquser = $this->em->getRepository('JiliApiBundle:QQUser')->qquser_insert($params);
            }
        }
        return $qquser;
    }
    
    /**
     * taobao UserBind
     * @params $params array()
     */
    public function taobao_user_bind(array $params)
    {
        $qquser = null;
        if (isset($params['email']) && isset($params['open_id'])) {
            $user = $this->em->getRepository('JiliApiBundle:User')->findOneBy(array('email'=> $params['email']));
            if( $user) {
                $params['user_id'] =  $user->getId();
                $qquser = $this->em->getRepository('JiliApiBundle:TaoBaoUser')->taobao_user_insert($params);
            }
        }
        return $qquser;
    }
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
    
    
}