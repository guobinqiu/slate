<?php
namespace Jili\ApiBundle\EventListener;

/**
 * 
 **/
class CpsAccessHistoryFactory 
{

    private $em;
    private $logger;

    public function setEntityManager( \Doctrine\ORM\EntityManager $em)  {
        $this->em = $em;
    }

    public function setLogger( \Symfony\Component\HttpKernel\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function log( $params ) {
        extract($params);
        $incentive_type = (int) $advertiserment->getIncentiveType();

        if($incentive_type === 1  ||  $incentive_type === 2 ) {
            $access_history_class = 'Jili\ApiBundle\Entity\AdwAccessHistory';
        } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
            $access_history_class = 'Jili\EmarBundle\Entity\EmarAccessHistory';
        }  else {
            $this->logger->crit('{ApiBundle:CpsAccessHistory}'. implode(',', array(__FILE__,__LINE__, '') ). var_export($incentive_type , true) );
        } 

        $uid = $request->getSession()->get('uid');

        if ( class_exists( $access_history_class) && isset($uid) && ! empty($uid)  ) {

            $accessHistory= new $access_history_class;
            $accessHistory->setUserId($uid);

            if($incentive_type === 1  ||  $incentive_type === 2 ) {
                $accessHistory->setAdId($advertiserment->getId() );
            } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
                $accessHistory->setAd($advertiserment);
            }

            $accessHistory->setAccessTime(date_create(date('Y-m-d H:i:s')));

            $this->em->persist($accessHistory);
            return $this->em->flush();
        } else {
            $this->logger->crit(implode(',',array(__FILE__, __LINE__, '')  ) . 'class ' . $access_history_class. ' not defined');
        }
        return $accessHistory;
    }
    public function setContainer( $c) {
        $this->container_ = $c;
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }
}
