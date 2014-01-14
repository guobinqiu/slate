<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

class ExchangeDangerRepository extends EntityRepository
{

    public function findByExchangeIds(array $exchange_ids)
    {
        $r = array();
        if (0 < count($exchange_ids)) {
            $qb = $this->createQueryBuilder('ed');

            $qb = $qb->Where($qb->expr()->in('ed.exchangeId', $exchange_ids));
            $qb = $qb->getQuery();

            $data = $qb->getResult(Query::HYDRATE_OBJECT);


            foreach ($data as $d) {
                $eid = $d->getExchangeId();

                if( isset($r [ $eid]) ) {
                    $r [$eid][] = $d;
                } else {
                    $r [$eid] = array($d);
                }
            }

        }
        return $r;
    }

}
