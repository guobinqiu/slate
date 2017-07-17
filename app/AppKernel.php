<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\JobQueueBundle\JMSJobQueueBundle(),
        	new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Jili\ApiBundle\JiliApiBundle(),
            new Jili\BackendBundle\JiliBackendBundle(),
            new Jili\FrontendBundle\JiliFrontendBundle(),
            new Wenwen\FrontendBundle\WenwenFrontendBundle(),
            new Wenwen\AppBundle\WenwenAppBundle(),
            new Affiliate\AppBundle\AffiliateAppBundle(),
            new Misd\GuzzleBundle\MisdGuzzleBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Captcha\Bundle\CaptchaBundle\CaptchaBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test', 'staging'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
