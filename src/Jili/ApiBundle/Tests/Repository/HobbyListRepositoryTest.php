<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Jili\ApiBundle\DataFixtures\ORM\LoadMergedUserData;

class HobbyListRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new LoadMergedUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetHobbyName()
    {
        $em = $this->em;

        $id = 13;
        $result = $em->getRepository('JiliApiBundle:HobbyList')->getHobbyName($id);
        $this->assertEquals('', $result, 'id:13, hobby not exist, return empty string');

        $id = 1;
        $result = $em->getRepository('JiliApiBundle:HobbyList')->getHobbyName($id);
        $this->assertEquals('上网', $result, 'id:1, hobby exist, return 上网');
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetUserHobbyName()
    {
        $em = $this->em;
        $user_hobby = null;
        $result = $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user_hobby);
        $this->assertEquals('', $result, 'user_hobby: null, return empty string');

        $user_hobby = '';
        $result = $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user_hobby);
        $this->assertEquals('', $result, 'user_hobby: empty string, return empty string');

        $user_hobby = 1;
        $result = $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user_hobby);
        $this->assertEquals('上网', $result, 'user_hobby: 1, return 上网');

        $user_hobby = '1,2,13';
        $result = $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user_hobby);
        $this->assertEquals('上网,音乐', $result, 'user_hobby: 1,2,13, return 上网,音乐');
    }
}
