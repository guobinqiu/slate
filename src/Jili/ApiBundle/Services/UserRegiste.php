<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserRegiste
{
    /**
     * UserRegiste
     * @params $params array()
     */
    public function registe(array $params)
    {
        $logger = $this->logger;
        if (isset($params ['email'] ) ) {
            $this->em->getRepository('JiliApiBundle:User')
                ->insert($params);
        }
        return $this;
    }
}