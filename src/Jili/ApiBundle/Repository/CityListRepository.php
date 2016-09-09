<?php
namespace Jili\ApiBundle\Repository;

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

}