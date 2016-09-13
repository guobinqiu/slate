<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wenwen\FrontendBundle\Command\PushBasicProfileCommand;

class PushBasicProfileCommandTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $this->em = $em;

        $loader = new Loader();
        $loader->addFixture(new LoadUserData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    public function testPushBasicProfileCommand() {
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();

        $application = new Application(static::$kernel);
        $application->add(new PushBasicProfileCommand());
        $command = $application->find('sop:push_basic_profile');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--user_id' => $users[0]->getId(),
        ));
        $output = $commandTester->getDisplay();
        $this->assertTrue($output);
    }
}

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick(__CLASS__);
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $userProfile = new UserProfile();
        $userProfile
            ->setUser($user)
            ->setSex(1)
            ->setBirthday('2016-01-01')
            ->setProvince(1)
            ->setCity(2);
        $manager->persist($userProfile);
        $manager->flush();
    }
}