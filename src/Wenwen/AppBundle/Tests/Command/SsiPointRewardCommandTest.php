<?php

namespace Wenwen\AppBundle\Tests\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Jili\ApiBundle\Utility\DateUtil;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SsiPointRewardCommandTest extends KernelTestCase
{
    protected static $kernel;

    public function setUp()
    {
        static::$kernel = static::createKernel(array(
            'environment' => 'test',
            'debug' => false,
        ));
        static::$kernel->boot();

        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new SsiPointRewardCommandTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = static::$kernel->getContainer();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }


    public function testExecuteInDefinitiveMode()
    {
        $kernel = self::$kernel;

        // mock the Kernel or create one depending on your needs
        $application = new Application($kernel);
        $application->add(new \Wenwen\AppBundle\Command\SsiPointRewardCommand());
        $command = $application->find('panel:reward-ssi-point');
        $command->setContainer($this->container);

        $iterator = \Phake::partialMock('\Wenwen\AppBundle\Services\SsiConversionReportIterator');
        \Phake::when($iterator)->getConversionReport(1)->thenReturn([
            'success' => true,
            'totalNumRows' => 1001,
            'data' => [self::getConversionRowSample(), self::getConversionRowSample()],
        ]);
        \Phake::when($iterator)->getConversionReport(2)->thenReturn([
            'success' => true,
            'totalNumRows' => 1001,
            'data' => [self::getConversionRowSample(), self::getConversionRowSampleSsiRespondentNotFound()],
        ]);
        $this->container->set('ssi_api.conversion_report_iterator', $iterator);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--definitive' => true));

        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById(SsiPointRewardCommandTestFixture::$USER->getId());
        $this->assertSame(1000, $user->getPoints());

        $rows = $this->em->getRepository('WenwenAppBundle:SsiProjectParticipationHistory')->findBySsiRespondentId(
            SsiPointRewardCommandTestFixture::$SSI_RESPONDENT->getId()
        );
        $this->assertCount(3, $rows);
    }



    private static function getConversionRowSample()
    {
        return array(
         'offer' => '1346 API_USD',
         'date_time' => date('Y-m-d H:i:s'),
         'source' => '',
         'sub_id' => '',
         'sub_id_1' => '',
         'sub_id_2' => '',
         'sub_id_3' => '',
         'sub_id_4' => '',
         'sub_id_5' => 'wwcn-'.SsiPointRewardCommandTestFixture::$SSI_RESPONDENT->getId(),
         'payout' => '$1.50',
         'ip' => '123.456.789.123',
         'status' => 'approved',
         'transaction_id' => md5(time().rand()),
        );
    }

    private static function getConversionRowSample2()
    {
        return array (
            'offer' => '1346 API_USD',
            'date_time' => '2016-05-12 13:54:53',
            'source' => '',
            'sub_id' => '',
            'sub_id_1' => '',
            'sub_id_2' => '',
            'sub_id_3' => '',
            'sub_id_4' => '',
            'sub_id_5' => 'wwcn-' . SsiPointRewardCommandTestFixture::$SSI_RESPONDENT->getId(),
            'payout' => '$1.50',
            'ip' => '123.456.789.123',
            'status' => 'approved',
            'transaction_id' => '102a8857d5db3fb1679cf1c204337b'
        );
    }

    private static function getConversionRowSampleSsiRespondentNotFound()
    {
        return array (
            'offer' => '1346 API_USD',
            'date_time' => '2016-05-12 13:54:53',
            'source' => '',
            'sub_id' => '',
            'sub_id_1' => '',
            'sub_id_2' => '',
            'sub_id_3' => '',
            'sub_id_4' => '',
            'sub_id_5' => 'wwcn-' . '1234',
            'payout' => '$1.50',
            'ip' => '123.456.789.123',
            'status' => 'approved',
            'transaction_id' => '102a8857d5db3fb1679cf1c204337b'
        );
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiPointRewardCommandTestFixture implements FixtureInterface, ContainerAwareInterface
{
    public static $USER, $SSI_RESPONDENT;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \Wenwen\FrontendBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();

        self::$USER = $user;
        self::$SSI_RESPONDENT = $ssi_respondent;
    }
}
