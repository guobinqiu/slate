<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSeptemberActivityData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    /**
    * @var ContainerInterface
    */
    private $container;

    public function __construct() {

    }

    /**
    * {@inheritDoc}
    */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
    * {@inheritDoc}
    */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures';
        $sql = file_get_contents($fixture_dir . DIRECTORY_SEPARATOR . 'user.sql');
        $r = $manager->getConnection()->query($sql);
        $r->closeCursor();
        for ($i = 0; $i < 10; $i++) {
            $sql = file_get_contents($fixture_dir . DIRECTORY_SEPARATOR . 'task_history0' . $i . '.sql');
            $r = $manager->getConnection()->query($sql);
            $r->closeCursor();
        }
    }
}