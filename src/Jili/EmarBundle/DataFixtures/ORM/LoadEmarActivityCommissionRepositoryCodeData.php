<?php

namespace Jili\EmarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadEmarActivityCommissionRepositoryCodeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface 
{

    /**
    * @var ContainerInterface
    */
    private $container;

    /**
    * {@inheritDoc}
    */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR ;
        
        $manager->clear();
        $r = $manager->getConnection()->query(file_get_contents($fixture_dir.DIRECTORY_SEPARATOR.'emar_activity_commission.sql'));
        $r->closeCursor();
    }
}

?>
