<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Jili\FrontendBundle\Entity\TaobaoCategory;

class TaobaoCategoryRepository extends EntityRepository 
{

    public function findCategorys($delete_flag = 0, $union_product = null) 
    {
        if(is_null($union_product) ){
            $union_product = TaobaoCategory::COMPONENTS;
        }
        $query = $this->createQueryBuilder('tca');
        $query = $query->select('tca');
        $query = $query->Where('tca.deleteFlag = :deleteFlag');
        $query = $query->AndWhere('tca.unionProduct= :unionProduct');
        $query = $query->setParameters(array (
            'deleteFlag' => $delete_flag,
            'unionProduct' => $union_product
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }
}
