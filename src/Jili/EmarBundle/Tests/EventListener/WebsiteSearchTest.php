<?php
namespace Jili\EmarBundle\Tests\EventListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WebsiteSearchTest extends KernelTestCase {

    /**
     * 
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group sameCatWeb
     */
    public function testFindSameCatWebsites() {
        $em = $this->em;
        $web_raw = array();
        $web_raw[] = array('web_id'=>1,'web_catid'=>1);
        $web_raw[] = array('web_id'=>2,'web_catid'=>2);
        $web_raw[] = array('web_id'=>3,'web_catid'=>3);
        $web_raw[] = array('web_id'=>4,'web_catid'=>4);
        $web_raw[] = array('web_id'=>5,'web_catid'=>5);
        $web_raw[] = array('web_id'=>6,'web_catid'=>5);
        $web_raw[] = array('web_id'=>7,'web_catid'=>5);
        $web_raw[] = array('web_id'=>8,'web_catid'=>5);
        
        $this->get('website.search')->findSameCatWebsites( $web_raw, 5 ,8);
        $this->assertCount(3, $result);
        
        $this->get('website.search')->findSameCatWebsites( $web_raw, 5 ,1);
        $this->assertCount(4, $result);
        
        $this->get('website.search')->findSameCatWebsites( $web_raw, 1 ,2);
        $this->assertCount(1, $result);
        
        $this->get('website.search')->findSameCatWebsites( $web_raw, 1 ,1);
        $this->assertCount(0, $result);
    }
}