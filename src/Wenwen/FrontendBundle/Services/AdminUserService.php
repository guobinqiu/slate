<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\User;

/**
 * AdminSurvey
 */
class AdminUserService
{
    private $logger;

    private $em;

    private $parameterService;

    private $knp_paginator;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                $knp_paginator)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->knp_paginator = $knp_paginator;
    }

    public function getSurveySopList($page, $limit = 10){

    }

    /**
     * 查找一个问卷项目
     * @param $surveryId
     */
    public function findUserTaskHistories($userId, $page, $limit = 10) {

        $pagination = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($userId % 10))->getByUserId($userId, $this->knp_paginator, $page, $limit);
        return $pagination;
    }


}
