<?php
namespace Affiliate\AppBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliatePartner;
use Affiliate\AppBundle\Services\AdminProjectService;;



class AdminProjectServiceTest extends WebTestCase
{

    private $em;

    private $application;

    private $adminProjectService;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->adminProjectService = static::$kernel->getContainer()->get('app.admin_project_service');
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
    public function testGetProjectList_ok(){
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
        
        $page = 1;
        $limit = 10;
        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_INIT;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $originalFileName2 = 'test2.txt';
        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName2);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $rtn = $this->adminProjectService->getProjectList($affiliatePartner->getId(), $page, $limit);

        //var_dump($pagination);
        $this->assertEquals('success', $rtn['status'], "Should find 2 projects.");

        // 检查projects是否被正确的取到
        $this->assertEquals(2, sizeof($rtn['pagination']), "Should find 2 projects.");
    }

    public function testAsynchUploadUrl_ok(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $projectId = 1;
        $fullPath = '/xxx/xxx/xxx.txt';
        $this->adminProjectService->asynchUploadUrl($projectId, $fullPath);

        $job = $this->em->getRepository('JMSJobQueueBundle:Job')->findOneByQueue(AdminProjectService::QUEUE_NAME);

        printf($job->getCommand());
        $this->assertEquals(1, sizeof($job));
    }

    public function testValidateProjectStatus_true(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
 
        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $rtn = $this->adminProjectService->validateProjectStatus($affiliateProject->getId());

        $this->assertEquals('success', $rtn['status']);
    }

    /**
    * projectId exist but status is not open
    */ 
    public function testValidateProjectStatus_false1(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $RFQId = 666;
        $originalFileName1 = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_INIT;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName1);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $rtn = $this->adminProjectService->validateProjectStatus($affiliateProject->getId());

        $this->assertEquals('failure', $rtn['status']);
    }

    /**
    * projectId not exist
    */ 
    public function testValidateProjectStatus_false2(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
 
        $rtn = $this->adminProjectService->validateProjectStatus(1);

        $this->assertEquals('failure', $rtn['status']);
    }

    public function testInitProject_sucess(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $RFQId = 777;
        $originalFileName = 'test1.txt';
        $fullPath = '/xxx/xxx/xxx.txt';
        $province = '上海市';
        $city = '上海市';

        $rtn = $this->adminProjectService->initProject($affiliatePartner->getId(), $RFQId, $originalFileName, $fullPath, $province, $city);

        $this->assertEquals('success', $rtn['status']);

        $job = $this->em->getRepository('JMSJobQueueBundle:Job')->findOneByQueue(AdminProjectService::QUEUE_NAME);

        $this->assertEquals(1, sizeof($job));

        $param = array(
            'affiliatePartner' => $affiliatePartner,
            'RFQId' => $RFQId
            );
        $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneBy($param);

        $this->assertEquals($originalFileName, $affiliateProject->getOriginalFileName());
        $this->assertEquals($fullPath, $affiliateProject->getRealFullPath());
        $this->assertEquals(AffiliateProject::PROJECT_STATUS_INIT, $affiliateProject->getStatus());

    }

    public function testInitProject_failure(){
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $partnerId = 1;
        $RFQId = 777;
        $originalFileName = 'test1.txt';
        $fullPath = '/xxx/xxx/xxx.txt';

        $rtn = $this->adminProjectService->initProject($partnerId, $RFQId, $originalFileName, $fullPath);

        $this->assertEquals('failure', $rtn['status']);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    public function testOpenProject_sucess(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $affiliatePartner = new AffiliatePartner();
        $affiliatePartner->setName('这只是一个测试');
        $affiliatePartner->setName('这只是一个测试的说明');
        $this->em->persist($affiliatePartner);
        $this->em->flush();

        $RFQId = 666;
        $originalFileName = 'test1.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_INIT;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $affiliateProject->setCompletePoints(0);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $initNum = 777;

        $rtn = $this->adminProjectService->openProject($affiliateProject->getId(), $initNum);

        $this->assertEquals('success', $rtn['status']);

        $this->assertEquals($originalFileName, $affiliateProject->getOriginalFileName());
        $this->assertEquals($realFullPath, $affiliateProject->getRealFullPath());
        $this->assertEquals(AffiliateProject::PROJECT_STATUS_OPEN, $affiliateProject->getStatus());
        $this->assertEquals($initNum, $affiliateProject->getInitNum());
    }

}
