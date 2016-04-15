<?php

namespace Wenwen\AppBundle\Tests\Repository;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SsiRespondentRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new SsiRespondentRepositoryTestFixture();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testRepositoryExists()
    {
        $repository = $this->em->getRepository('WenwenAppBundle:SsiRespondent');
        $this->assertInstanceOf('\Wenwen\AppBundle\Repository\SsiRespondentRepository', $repository);
    }
    public function testRetrieveRecipientDataToSendMailById()
    {
        $recipient_data = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->retrieveRecipientDataToSendMailById(99);
        $this->assertEmpty($recipient_data);

        $recipient_data = $this->em->getRepository('WenwenAppBundle:SsiRespondent')
            ->retrieveRecipientDataToSendMailById(SsiRespondentRepositoryTestFixture::$SSI_RESPONDENT->getId());
        $this->assertNotEmpty($recipient_data);
        $this->assertEquals('test@d8aspring.com', $recipient_data['email']);
        $this->assertEquals(__NAMESPACE__.'\SsiRespondentRepositoryTestFixture', $recipient_data['name1']);
        $this->assertEquals('先生', $recipient_data['title']);
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiRespondentRepositoryTestFixture implements FixtureInterface, ContainerAwareInterface
{
    public static $SSI_RESPONDENT;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setIsEmailConfirmed(1);
        $user->setPwd('password');
        $manager->persist($user);
        $manager->flush();

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();

        self::$SSI_RESPONDENT = $ssi_respondent;
    }
}
