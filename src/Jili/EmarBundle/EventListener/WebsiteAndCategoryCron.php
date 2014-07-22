<?php
namespace Jili\EmarBundle\EventListener;


use Jili\EmarBundle\Entity\EmarWebsitesCategoryCron as EmarWebsitesCategoryCron;

class WebsiteAndCategoryCron extends BaseCron
{
    public function __construct()
    {
        $this->class_name_croned = 'JiliEmarBundle:EmarWebsitesCategory';
        $this->class_name_cron = 'JiliEmarBundle:EmarWebsitesCategoryCron';
    }
    public function add($wid, $catid)
    {
        $em = $this->em;

        $logger  = $this->logger;
        $logger->debug('{jaord}'. implode(':', array(__CLASS__,__LINE__,'$wid','')). var_export( $wid ,true));
        $logger->debug('{jaord}'. implode(':', array(__CLASS__,__LINE__,'$catid','')). var_export( $catid ,true));

        // $cmd_cron = $em->getClassMetadata($this->class_name_cron );
        $entity = $em->getRepository( $this->class_name_cron)->findOneBy(array('webId'=>$wid, 'categoryId'=> $catid ) );

        if( ! $entity) {
            $entity = new EmarWebsitesCategoryCron;
            $entity->setWebId($wid);
            $entity->setCategoryId($catid);
            $entity->setCount( 1 );
        } else {
            $entity->setCount( 1 + $entity->getCount());
        }
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

}
