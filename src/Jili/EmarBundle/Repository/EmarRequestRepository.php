<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\EmarBundle\Entity\EmarRequest;

/**
 */
class EmarRequestRepository extends EntityRepository
{

#    public function doIncrement( $tag )
#    {
#
#        if( empty($tag)) {
#            $tag = date('YmdHi');
#        }
#
#        $em = $this->getEntityManager();
#        $row = $this->findOneByTag( $tag );
#        if( ! $row) {
#            $row = new  EmarRequest;
#            $row->setCount(1);
#            $row->setTag($tag);
#        }
#
#    }

}
