<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class TaobaoComponentRepository extends EntityRepository {

    public function findByCategory($category_id, $component_id) {
        $query = $this->createQueryBuilder('tc');
        $query = $query->select('tc');
        $query = $query->Where('tc.componentId = :componentId');
        $query = $query->andWhere('tc.categoryId = :categoryId');
        $query = $query->setParameters(array (
            'componentId' => $component_id,
            'categoryId' => $category_id
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

}