<?php

namespace Jili\ApiBundle\DataFixtures\ORM\Command\PointRecent;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Finder\Finder;

class LoadPointHistoryData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

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
    public function getOrder() {
        return 3; // the order in which fixtures will be loaded
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $root_dir = $this->container->get('kernel')->getRootDir();

        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR. 'point_recent'.DIRECTORY_SEPARATOR.'20140304';


        $manager->clear();
        $finder  = new Finder();
        $finder->files()->in($fixture_dir)->name('/^point_history0[0-9].sql$/');
        foreach ($finder as $file) {
            $r = $manager->getConnection()->query(file_get_contents($file->getRealpath()));
            $r->closeCursor();
        }

    }
}

?>
