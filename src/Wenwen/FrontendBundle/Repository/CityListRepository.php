<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CityListRepository extends EntityRepository
{
    public function findOneCityByNameLike($cityName)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder = $queryBuilder->select('c.cityName, c.cityId, c.provinceId');
        $queryBuilder->andWhere('c.cityName like :cityName');

        $parameters['cityName'] = '%' . $cityName . '%';
        $queryBuilder->setParameters($parameters);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getCitiesByProvinceId($provinceId) {
        return $this->createQueryBuilder('c')
            ->select('c.cityName, c.cityId')
            ->where('c.provinceId = ?1')
            ->setParameter(1, $provinceId)->getQuery()->getResult();
    }
}