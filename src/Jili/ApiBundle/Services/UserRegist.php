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
    public function qq_user_regist(array $params)
    {
        if (isset($params['email']) && isset($params['open_id']) && isset($params['pwd']) && isset($params['nick'])) {
            $connection = $this->em->getConnection();
            $connection->beginTransaction();
            try {
                $user = $this->em->getRepository('JiliApiBundle:User')->qquser_quick_insert($params);
                $params['pwd'] = $user->pw_encode($params['pwd']);
                $params['user_id'] =  $user->getId();
                $qquser = $this->em->getRepository('JiliApiBundle:QQUser')->qquser_insert($params);
                $connection->commit();
                return $qquser;
            } catch (Exception $ex) {
                $connection->rollback();
            }
        } 
        return null;
    }
    
    /**
     * UserRegiste
     * @params $params array()
     */
    public function taobao_user_regist(array $params)
    {
        if (isset($params['email']) && isset($params['open_id']) && isset($params['pwd']) && isset($params['nick'])) {
            $connection = $this->em->getConnection();
            $connection->beginTransaction();
            try {
                $user = $this->em->getRepository('JiliApiBundle:User')->taboabo_user_quick_insert($params);
                $params['pwd'] = $user->pw_encode($params['pwd']);
                $params['user_id'] =  $user->getId();
                $qquser = $this->em->getRepository('JiliApiBundle:TaoBaoUser')->taobao_user_insert($params);
                $connection->commit();
                return $qquser;
            } catch (Exception $ex) {
                $connection->rollback();
            }
        } 
        return null;
    }
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
}
