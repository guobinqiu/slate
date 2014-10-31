<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class TaobaoCategoryRepository extends EntityRepository {

    public function findCategorys($delete_flag = 0) {
        $query = $this->createQueryBuilder('tca');
        $query = $query->select('tca');
        $query = $query->Where('tca.deleteFlag = :deleteFlag');
        $query = $query->setParameters(array (
            'deleteFlag' => $delete_flag
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

}