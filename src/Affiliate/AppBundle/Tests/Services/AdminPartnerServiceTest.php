<?php
namespace Affiliate\AppBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliatePartner;



class AdminParterServiceTest extends WebTestCase
{

    private $em;

    private $application;

    private $adminPartnerService;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->adminPartnerService = static::$kernel->getContainer()->get('app.admin_partner_service');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }


    /**
    */
    public function testValidatePartnerStatus_success(){
        // 删掉所有表
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));

        // 测试结束，恢复所有表
        // 建立所有表
        //$this->runConsole("doctrine:schema:create");

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $rtn = $this->adminPartnerService->validatePartnerStatus($affiliatePartner->getId());

        $this->assertEquals('success', $rtn['status'], "status should be success");
    }

    public function testValidatePartnerStatus_failure(){
        // 删掉所有表
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));

        // 测试结束，恢复所有表
        // 建立所有表
        //$this->runConsole("doctrine:schema:create");

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $rtn = $this->adminPartnerService->validatePartnerStatus(1);

        $this->assertEquals('failure', $rtn['status'], "status should be failure");
    }

    public function testGetPartnerList_success(){
        // 删掉所有表
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));

        // 测试结束，恢复所有表
        // 建立所有表
        //$this->runConsole("doctrine:schema:create");

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $rtn = $this->adminPartnerService->getPartnerList(1,10);

        $this->assertEquals(1, sizeof($rtn), "size should be 1");
    }

    public function testAddPartner_success(){
        // 删掉所有表
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));

        // 测试结束，恢复所有表
        // 建立所有表
        //$this->runConsole("doctrine:schema:create");

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $name = "わはは";
        $description = "あははは";

        $rtn = $this->adminPartnerService->addPartner($name, $description);

        $this->assertEquals('success', $rtn['status'], "status should be success");
    }

    public function testAddPartner_failure(){
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $name = "わはは";
        $description = "あははは";

        $rtn = $this->adminPartnerService->addPartner($name, $description);

        $this->assertEquals('failure', $rtn['status'], "status should be failure");

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

}
