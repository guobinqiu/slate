<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadExperienceAdvertisementCodeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface{

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
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures' ;
        $file = $fixture_dir.DIRECTORY_SEPARATOR. 'experience_advertisement.sql';
        $r = $manager->getConnection()->query(file_get_contents($file));
        $r->closeCursor();
    }
}

?>
