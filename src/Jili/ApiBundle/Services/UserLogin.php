<?php
namespace Jili\ApiBundle\Services;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\User;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserLogin
{
    private $em;
    /**
     * UserRegiste
     * @params $params array()
     */
    public function checkLoginStatus()
    {
        $session = $this->container->get('session');
        if( $session->has('uid')){
            var_dump($session->get('uid'));
            return true;
        }
        return false;
    }
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
}