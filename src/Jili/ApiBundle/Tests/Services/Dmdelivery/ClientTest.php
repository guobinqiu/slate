<?php
namespace  Jili\ApiBundle\Tests\Services\Dmdelivery;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class ClientTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

//        $fixture = new LoadExchangeFlowOrderData();
//        $loader = new Loader();
//        $loader->addFixture($fixture);
//        $executor->execute($loader->getFixtures());

        $this->container = static :: $kernel->getContainer();
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    public function testDemo() 
    {
        $container= $this->container;
        $client = $container->get('soap.mail.listener');
        $this->assertInstanceOf( '\Jili\ApiBundle\Services\Dmdelivery\Client', $client);
    }

    public function testBuildRecipientData() 
    {
        $container= $this->container;
        $client = $container->get('webpower.91wenwen_signup.mailer');

        $return = $client->buildRecipientData( array( array('a'=>1,'b'=>2) ));
        $expected = array('recipients'=> array( array('fields'=> array( 
            array( 'name'=> 'a', 'value'=> 1) , 
            array( 'name'=> 'b', 'value'=> 2) , 
        ) ) )) ;
        $this->assertEquals( $expected,$return, 'the reciepientData required by soap api'); 

        $return = $client->buildRecipientData( array( array('a'=>1,'b'=>2), array('c'=>3, 'd'=>4) ));

        $expected = array('recipients'=> array( array('fields'=> array( 
            array( 'name'=> 'a', 'value'=> 1) , 
            array( 'name'=> 'b', 'value'=> 2) , 
        ) ),array('fields'=> array( 
            array( 'name'=> 'c', 'value'=> 3) , 
            array( 'name'=> 'd', 'value'=> 4) , 
        ) ) )) ;
        $this->assertEquals( $expected,$return, 'the reciepientData required by soap api'); 
    }

    public function testSignupConfirm() 
    {
        $container= $this->container;
        $client = $container->get('webpower.91wenwen_signup.mailer');
        $this->assertInstanceOf('\Jili\ApiBundle\Services\Dmdelivery\Client', $client);

        $client->setCampaignId(1)  # 91wenwen-signup
            ->setMailingId(9) # signup-mail-20110609
            ->setGroupId(81);  # signup-recipients

        $recipient = array(
            'email'         => 'tao.jiang@d8aspring.com',
            'name'          => 'Jarod',
            'title'         => 'test'  . date('Y-m-d H:i:s') ,
            'register_key'  => 'register_key'.date('YmdHis'));

        $this->markTestSkipped('call signleEmail() will send email out actually');

        $return = $client->singleEmail($recipient);
        $this->assertEquals($return ,'Email send success','email should be send successfull');
    }
}
