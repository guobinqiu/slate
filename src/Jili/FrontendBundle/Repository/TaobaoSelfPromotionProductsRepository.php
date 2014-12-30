<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\FrontendBundle\Entity\TaobaoSelfPromotionProducts;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

use Jili\ApiBundle\Utility\FileUtil;

class TaobaoSelfPromotionProductsRepository extends EntityRepository 
{



    /**
     * @return array
     */
    public function fetchByRange( $page_no = 1, $page_size= 10, $filters = array()  )
    {
        $em = $this->getEntityManager();

        $qb= $this->createQueryBuilder('o');
        $qb->select($qb->expr()->count('o') );
        $total = $qb->getQuery()->getSingleScalarResult();

        $limit = $page_size; 
        $offset = $page_size * ($page_no - 1); 
//        $qb->orderBy('o.createdAt DESC, o.id', 'DESC');
        $qb->select('o');
        $qb->setFirstResult( $offset )
            ->setMaxResults( $limit );
        $rows = $qb->getQuery()->getResult();
        return array('total'=> $total, 'data'=> $rows);
    }

    /**
     * @return array  
     */
    public function fetch()
    {
        $em = $this->getEntityManager();

        $qb= $this->createQueryBuilder('p');
        $qb->orderBy('p.taobaoCategory', 'ASC');
        $qb->addOrderBy('p.id', 'DESC');
        $rows = $qb->getQuery()->getResult();
        return  $rows ;
    }


}
