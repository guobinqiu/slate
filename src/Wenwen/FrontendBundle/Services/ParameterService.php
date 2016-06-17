<?php

namespace Wenwen\FrontendBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class ParameterService
{
    private $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getParameter($param_name){
        return $this->container->getParameter($param_name);
    }
}
