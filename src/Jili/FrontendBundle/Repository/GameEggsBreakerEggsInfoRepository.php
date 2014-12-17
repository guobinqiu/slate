<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

class GameEggsBreakerEggsInfoRepository extends EntityRepository 
{
    /**
     * @param array $params 
     * @return 
     */
    public function findOneOrCreateByUserId( $user_id )
    {
        $entity = $this->findOneByUserId( $user_id );
        if(! $entity) {
            $entity = new GameEggsBreakerEggsInfo();
            $entity->setUserId($user_id)
                ->refreshToken();

            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();
        }
        return $entity;
    }

}
