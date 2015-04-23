<?php

namespace Jili\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommissionDataBase
 *
 * @ORM\MappedSuperclass 
 */
class CommissionDataBase
{

    public function getCommissionForUser($percentage ) 
    {
        $current = $this->getCommission();

        preg_match('/[^\d]*(\d+\.?\d*)[^\d]*/', $current, $matches );

        if (2 === count($matches)) {
            $after =  str_replace($matches[1], (string) (( $matches[1] * $percentage ) / 100) , $current);
        }else {
            $after  = $current;
        }
        return $after;
    }

}

