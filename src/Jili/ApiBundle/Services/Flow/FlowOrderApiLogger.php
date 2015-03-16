<?php
namespace Jili\ApiBundle\Services\Flow;

use Symfony\Bridge\Monolog\Logger;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\FlowOrderApiReturn;

/**
 *
 **/
class FlowOrderApiLogger {

    private $em;
    private $logger;

    public function __construct(Logger $logger, $em) {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     *
     * @param  $content the request uri of Adw
     *
     */
    public function log($content) {
        $api = new FlowOrderApiReturn();
        $api->setContent($content);
        $api->setCreatedAt(date_create());
        $this->em->persist($api);
        return $this->em->flush();
    }

}