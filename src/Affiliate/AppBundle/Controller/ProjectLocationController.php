<?php
namespace Affiliate\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GetProjectLocation extends Controller
{
    private $logger;

    private $em;

    private $knp_paginator;

    private $router;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                $knp_paginator,
                                $router)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->knp_paginator = $knp_paginator;
        $this->router = $router;
    }

    public function getProjectLocation($affiliateProjectId){
        #$rtn = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->find();
        #var_dump $affiliateProjectId;
        return $affiliateProjectId;   
    }
}
        
