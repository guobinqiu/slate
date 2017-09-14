<?php

namespace Wenwen\FrontendBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 读取app/config/parameters.yml文件的配置信息
 */
final class ParameterService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getParameter($name)
    {
        try {
            return $this->container->getParameter($name);
        } catch (\Exception $e) {
            return null;
        }
    }
}