<?php
namespace Jili\ApiBundle\EventListener;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\PointHistory00,
    Jili\ApiBundle\Entity\PointHistory01,
    Jili\ApiBundle\Entity\PointHistory02,
    Jili\ApiBundle\Entity\PointHistory03,
    Jili\ApiBundle\Entity\PointHistory04,
    Jili\ApiBundle\Entity\PointHistory05,
    Jili\ApiBundle\Entity\PointHistory06,
    Jili\ApiBundle\Entity\PointHistory07,
    Jili\ApiBundle\Entity\PointHistory08,
    Jili\ApiBundle\Entity\PointHistory09;

/**
 * 
 **/
class PointHistory
{

    private $em;
    private $logger;

    public function __construct(LoggerInterface $logger, EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @param: $params = array (
     *                    'userid' => 1057622,
     *                    'point' => 17,
     *                    'type' => 1,
     *                  ) 
     */
    public function get(array $params = array() ){

        extract($params);

        $point_history = 'Jili\ApiBundle\Entity\PointHistory0'. ( $userid % 10);

        $po = new $point_history();

        $em = $this->em;
        $po->setUserId($userid);
        $po->setPointChangeNum($point);
        $po->setReason($type);
        $em->persist($po);
        $em->flush();

    }
}

