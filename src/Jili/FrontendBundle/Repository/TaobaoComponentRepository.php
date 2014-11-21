<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class TaobaoComponentRepository extends EntityRepository {

    public function findComponents($category_id, $component_id, $page = 1, $limit = 5) {
        $query = $this->createQueryBuilder('tc');
        $query = $query->select('tc.id, tc.componentId, tc.categoryId, tc.keyword, tc.content');
        $query = $query->Where('tc.componentId = :componentId');
        $query = $query->andWhere('tc.categoryId = :categoryId');
        $query = $query->setParameters(array (
            'componentId' => $component_id,
            'categoryId' => $category_id
        ));
        if (!is_null($page) || !is_null($limit)) {
            $query = $query->setFirstResult(($page -1) * $limit);
            $query = $query->setMaxResults($limit);
        }
        $query->orderBy('tc.sort', 'ASC');
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
    * @param $component: array('componentId'=>,'categoryId'=>,'keywordId'=>)
    */
    public function findComponentsByCondition($component) {
        $query = $this->createQueryBuilder('tc');
        $query = $query->select('tc.id, tc.componentId, tc.categoryId, tc.keyword, tc.content, tc.sort');
        //add keywordId condition
        if ($component['keywordId']) {
            $query = $query->Where('tc.id = :id');
            $param['id'] = $component['keywordId'];
        } else {
            //add componentId condition
            $query = $query->Where('tc.componentId = :componentId');
            $param['componentId'] = $component['componentId'];

            //add categoryId condition
            if ($component['categoryId']) {
                $query = $query->andWhere('tc.categoryId = :categoryId');
                $param['categoryId'] = $component['categoryId'];
            }
        }

        $query = $query->setParameters($param);

        $query->orderBy('tc.id', 'DESC');
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function findKeywordByCategoryId($categoryId) {
        $query = $this->createQueryBuilder('tc');
        $query = $query->select('tc.id, tc.keyword');
        $query = $query->Where('tc.categoryId = :categoryId');
        $query = $query->setParameters(array (
            'categoryId' => $categoryId
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

}