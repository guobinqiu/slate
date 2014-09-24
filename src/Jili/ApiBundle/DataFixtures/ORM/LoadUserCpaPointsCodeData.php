<?php

namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadUserCpaPointsCodeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface {

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

        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures'. DIRECTORY_SEPARATOR.'task_history'.DIRECTORY_SEPARATOR.'by_user';
        $user_sql = $fixture_dir .DIRECTORY_SEPARATOR.'user.sql';
    
        $r = $manager->getConnection()->query(file_get_contents($user_sql));
        $r->closeCursor();

        $task_history05_sql = $fixture_dir .DIRECTORY_SEPARATOR.'task_history05.sql';
        $r = $manager->getConnection()->query(file_get_contents($task_history05_sql));
        $r->closeCursor();
    }
}

?>
