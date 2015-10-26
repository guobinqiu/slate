<?php
namespace Jili\ApiBundle\Services\Weibo;

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
    private $session;
    /**
     * check login status
     * @params $params array()
     */
    public function checkLoginStatus()
    {
        if( $this->session->has('uid')){
            return true;
        }
        return false;
    }

    /**
     * getLoginUserId 
     * 
     * @access public
     * @return void
     */
    public function getLoginUserId()
    {
        if( $this->checkLoginStatus()) {
            return $this->session->get('uid');
        }
        return ;
    }
    
    /**
     * setEntityManager 
     * 
     * @param EntityManager $em 
     * @access public
     * @return void
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }
    
    /**
     * setSession 
     * 
     * @param mixed $session 
     * @access public
     * @return void
     */
    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }
}
