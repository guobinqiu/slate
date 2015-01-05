<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Jili\ApiBundle\Entity\UserAdvertisermentVisit;

class UserAdvertisermentVisitRepository extends EntityRepository {

    public function getAdvertisermentVisit($userid, $date) {
        $query = $this->createQueryBuilder('uad');
        $query = $query->select('uad.id,uad.visitDate');
        $query = $query->Where('uad.userId = :userid');
        $query = $query->andWhere('uad.visitDate = :date');
        $query = $query->setParameters(array (
            'userid' => $userid,
            'date' => $date
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function insert($params = array ()) {
        $em = $this->getEntityManager();
        $visit = new UserAdvertisermentVisit();
        $visit->setUserId($params['userId']);
        $visit->setVisitDate($params['date']);
        $em->persist($visit);
        $em->flush();
        return $visit;
    }
}