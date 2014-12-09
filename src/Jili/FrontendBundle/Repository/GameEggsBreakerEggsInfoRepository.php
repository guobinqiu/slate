<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\FrontendBundle\Entity\GameSeekerDaily;

class GameEggsBreakerEggsInfoRepository extends EntityRepository 
{

    /**
     *
     */
    public function getStat()
    {

    }

    /**
     * @param array $params 
     * @return 
     */
    public function updateOnAuditByUserId( $params )
    {
        if (! isset($params['userId']) ) {
            return ;
        }

        $entity = $this->findOneByUserId( $params['userId']);
        if( $entity) {
            $entity = new GameEggsBreakerEggsInfo( );
            $entity->setUserId($params['userId']);
        }

        return $entity;
    }

}
