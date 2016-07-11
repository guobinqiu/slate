<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Jili\ApiBundle\Entity\OfferwowOrder;
use Jili\ApiBundle\Entity\User;
use Wenwen\FrontendBundle\Services\OfferwowRequestService;

class OfferwowRequestControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $container;

    private $client;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = self::$kernel->getContainer();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->em != null) {
            $this->em->close();
        }
    }

    public function testGetInfoActionValidationFailed()
    {
        // prepare test data
        $parameter['memberid'] = '101';
        $parameter['point'] = '100';
        $parameter['websiteid'] = '200';
        $parameter['eventid'] = '1001';
        $parameter['immediate'] = '1';
        $parameter['programname'] = '1';
        $parameter['sign'] = 'test';


        $this->client->request(
            'GET',
            '/api/offerwow/getInfo',
            $parameter
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        print $response['memberid'];

        $expect_status = 'failure';
        $expect_errno = 'offerwow-02';

        $this->assertEquals($expect_status, $response['status']);
        $this->assertEquals($expect_errno, $response['errno']);

    }

    public function testGetInfoActionValidationSuccess()
    {
        // prepare test data
        $parameter['memberid'] = '101';
        $parameter['point'] = '100';
        $parameter['websiteid'] = $this->container->getParameter('offerwow_com.websiteid');
        $parameter['eventid'] = '1001';
        $parameter['immediate'] = '1';
        $parameter['programname'] = 'test';
        $hash = array(
            $parameter['memberid'],
            $parameter['point'],
            $parameter['eventid'],
            $parameter['websiteid'],
            $parameter['immediate'],
            $this->container->getParameter('offerwow_com.key')
            );
        $parameter['sign'] = strtoupper(md5(implode($hash))); // 计算md5的sign;

        // prepare test db
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        // 准备user数据
        $connection = $this->em->getConnection();
        $connection->exec("ALTER TABLE user AUTO_INCREMENT = ". $parameter['memberid'] .";");

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setIconPath('test');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        $happen_time = date_create();
        $offerwowOrder = new OfferwowOrder();
        $offerwowOrder->setUserid($parameter['memberid']); 
        $offerwowOrder->setEventid($parameter['eventid']);
        $offerwowOrder->setStatus(OfferwowRequestService::convertStatus('0'));   
        $offerwowOrder->setHappenedAt($happen_time);
        $offerwowOrder->setCreatedAt($happen_time);
        $offerwowOrder->setDeleteFlag(0);

        // test request 
        $this->client->request(
            'GET',
            '/api/offerwow/getInfo',
            $parameter
        );

        $json_response = $this->client->getResponse()->getContent();
        print $json_response . PHP_EOL;
        $response = json_decode($json_response, true);


        $expect_status = 'success';
        $this->assertEquals($expect_status, $response['status']);

    }

}
