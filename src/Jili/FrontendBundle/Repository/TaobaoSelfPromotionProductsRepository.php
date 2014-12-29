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
     *
     */
    public function insert($params)
    {
        $entity = new TaobaoSelfPromotionProducts();

        $entity->setTaobaoCategoryId($params['taobaoCategory']->getId() );

        $entity->setTitle($params['title']);

        $entity->setPrice($params['price']);
        $entity->setPricePromotion($params['pricePromotion']);

        $entity->setClickUrl($params['clickUrl']);
        
        if( isset($params['picture']) ){
            $name = FileUtil::moveUploadedFile( $params['picture'],$params['pic_target_path']);
            $entity->setPictureName($name);
        }

        if ( isset($params['itemUrl']) ) {
            $entity->setItemUrl($params['itemUrl']);
        }

        if ( isset($params['commentDescription']) ) {
            $entity->setCommentDescription($params['commentDescription']);
        }

        if ( isset($params['promotionRate']) ) {
            $entity->setPromotionRate($params['promotionRate']);
        }

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return $entity;
    }


    /**
     * @param array $params value to updated
     */
    public function updateOne($params)
    {

        if(!isset($params['id'])) {
            return ;
        }


    }

    /**
     * @param integer $id the id 
     */
    public function remove($id = 0, $picture_dir = '')
    {
        if($id <= 0) {
            return ;
        }
        $em = $this->getEntityManager();
        //remove the file if exists! 
        $entity = $this->findOneById($id) ;

        if( !$entity) {
            return ;
        }

        try {
            $picture_name = $entity->getPictureName();
            if( ! empty($picture_name)) {
                $fs  = new Filesystem();
                $target = $picture_dir. $picture_name;
                if ($fs->exists($target)) {
                    $fs->remove($target);
                }
            }

            $em->getConnection()->beginTransaction();
            $q = $em->createQuery('delete from Jili\FrontendBundle\Entity\TaobaoSelfPromotionProducts tbp where tbp.id = :id')
                ->setParameter('id', $id);

            $q->execute();
            $em->flush(); 
            $em->getConnection()->commit();
        } catch( \Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

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
        
        //$em->getRepository('JiliFrontendBundle:TaobaoCategory', 'tbc')
    }


}
