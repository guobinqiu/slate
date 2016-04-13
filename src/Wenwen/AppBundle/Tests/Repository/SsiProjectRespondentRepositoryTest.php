<?php
namespace Wenwen\AppBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class SsiProjectRespondentRepositoryTest extends KernelTestCase
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

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new SsiProjectRespondentRepositoryTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

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

    public function testRetrieveAllForRespondentCount()
    {
        $ssi_respondent = SsiProjectRespondentRepositoryTestFixture::$SSI_RESPONDENT;
        $return = $this->em->getRepository('WenwenAppBundle:SsiProjectRespondent')->retrieveAllForRespondentCount($ssi_respondent);
        $this->assertEquals(1, $return);
    }

    public function testRetrieveAllForRespondent()
    {
        $ssi_respondent = SsiProjectRespondentRepositoryTestFixture::$SSI_RESPONDENT;
        $return = $this->em->getRepository('WenwenAppBundle:SsiProjectRespondent')->retrieveAllForRespondent($ssi_respondent, 1, 1);
        $this->assertCount(1, $return);
        $this->assertEquals(1, $return[0]->getAnswerStatus());
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiProjectRespondentRepositoryTestFixture implements ContainerAwareInterface, FixtureInterface
{
    public static $USER;
    public static $SSI_RESPONDENT;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //load data for testing
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('test1');
        $user->setEmail('zhangmm@ec-navi.com.cn');
        $user->setIsEmailConfirmed(1);
        $user->setPwd('123qwe');
        $user->setPasswordChoice(\Jili\ApiBundle\Entity\User::PWD_JILI);
        $user->setCity(2);
        $manager->persist($user);
        $manager->flush();
        self::$USER = $user;

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag($ssi_respondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();
        self::$SSI_RESPONDENT = $ssi_respondent;

        $ssi_project = new \Wenwen\AppBundle\Entity\SsiProject();
        $ssi_project->setStatusFlag(1);
        $manager->persist($ssi_project);
        $manager->flush();

        $ssi_project_respondent = new \Wenwen\AppBundle\Entity\SsiProjectRespondent();
        $ssi_project_respondent->setSsiRespondent($ssi_respondent);
        $ssi_project_respondent->setSsiProject($ssi_project);
        $ssi_project_respondent->setSsiMailBatchId(1);
        $ssi_project_respondent->setStartUrlId('hoge');
        $ssi_project_respondent->setAnswerStatus(1);
        $ssi_project_respondent->setStashData(array (
            'startUrlHead' => 'http://www.d8aspring.com/?dummy=ssi-survey&id='
        ));
        $manager->persist($ssi_project_respondent);
        $manager->flush();
    }
}
