<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class TaobaoComponentRepository extends EntityRepository {

    public function findByCategory($category_id, $component_id, $page = 1, $limit = 4) {
        $query = $this->createQueryBuilder('tc');
        $query = $query->select('tc.id, tc.componentId, tc.categoryId, tc.keyword, tc.content');
        $query = $query->Where('tc.componentId = :componentId');
        $query = $query->andWhere('tc.categoryId = :categoryId');
        $query = $query->setParameters(array (
            'componentId' => $component_id,
            'categoryId' => $category_id
        ));
        if (!is_null($page) || !is_null($limit)) {
            $query = $query->setFirstResult(($page-1)*$limit);
            $query = $query->setMaxResults($limit);
        }
        $query = $query->getQuery();
        return $query->getResult();
    }

}