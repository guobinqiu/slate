<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;

use Jili\ApiBundle\Entity\DuomaiApiReturn;

class DuomaiApiReturnRepository extends EntityRepository
{
    /**
    * create the user when regist by weibo
    * @param String 'uri' ;
    * @return null 
    */
    public function log($uri = '')
    {
        if( strlen($uri) <= 0 ) {
            return ;
        }

        $entity = new DuomaiApiReturn();
        $entity->setContent($uri);
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }
}
