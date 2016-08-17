<?php
namespace Affiliate\AppBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliatePartner;



class AffiliateSurveyServiceTest extends WebTestCase
{

    private $em;

    private $application;

    private $affiliateSurveyService;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->affiliateSurveyService = static::$kernel->getContainer()->get('app.affiliate_survey_service');
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
    public function testGetSurveyUrl_foundInitUrl(){
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

        $RFQId = 666;
        $originalFileName = 'test.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;
        
        $uKey = '09901562asarccm88ui8';
        $surveyUrl  = "http://r.researchpanelasia.com.dev1.researchpanelasia.com/redirect/forward/784/1562/09901562asarccm88ui8";
        $urlStatus = AffiliateUrlHistory::SURVEY_STATUS_INIT;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setInitNum(1);

        $affiliateProject->setStatus($projectStatus);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $affiliateUrlHistory = new AffiliateUrlHistory();
        $affiliateUrlHistory->setUKey($uKey);
        $affiliateUrlHistory->setAffiliateProject($affiliateProject);
        $affiliateUrlHistory->setSurveyUrl($surveyUrl);
        $affiliateUrlHistory->setStatus($urlStatus);
        $this->em->persist($affiliateUrlHistory);
        $this->em->flush();
        $urlId = $affiliateUrlHistory->getId();

        $url = $this->affiliateSurveyService->getSurveyUrl($affiliateProject->getId());

        // 检查url是否被正确的取到
        $this->assertEquals($surveyUrl, $url);

        $result = $this->em->getRepository('AffiliateAppBundle:AffiliateUrlHistory')->findOneById($urlId);
        $status = $result->getStatus();

        // 检查状态是否被更改为forward
        $this->assertEquals(AffiliateUrlHistory::SURVEY_STATUS_FORWARD, $status);

        $this->assertEquals(0, $affiliateProject->getInitNum(),"InitNum should be updated to 0(1-1)");
    }

    /**
    */
    public function testGetSurveyUrl_notFoundInit(){
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

        $RFQId = 666;
        $originalFileName = 'test.txt';
        $realFullPath = '/xxx/xxx/xxx.txt';
        $projectStatus = AffiliateProject::PROJECT_STATUS_OPEN;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setStatus($projectStatus);
        $this->em->persist($affiliateProject);
        $this->em->flush();

        $uKey = '09901562asarccm88ui8';
        $surveyUrl  = "http://r.researchpanelasia.com.dev1.researchpanelasia.com/redirect/forward/784/1562/09901562asarccm88ui8";

        $status1 = AffiliateUrlHistory::SURVEY_STATUS_FORWARD;
        $affiliateUrlHistory1 = new AffiliateUrlHistory();
        $affiliateUrlHistory1->setUKey($uKey);
        $affiliateUrlHistory1->setAffiliateProject($affiliateProject);
        $affiliateUrlHistory1->setSurveyUrl($surveyUrl);
        $affiliateUrlHistory1->setStatus($status1);

        $status2 = AffiliateUrlHistory::SURVEY_STATUS_COMPLETE;
        $affiliateUrlHistory2 = new AffiliateUrlHistory();
        $affiliateUrlHistory2->setUKey($uKey);
        $affiliateUrlHistory2->setAffiliateProject($affiliateProject);
        $affiliateUrlHistory2->setSurveyUrl($surveyUrl);
        $affiliateUrlHistory2->setStatus($status2);

        $status3 = AffiliateUrlHistory::SURVEY_STATUS_SCREENOUT;
        $affiliateUrlHistory3 = new AffiliateUrlHistory();
        $affiliateUrlHistory3->setUKey($uKey);
        $affiliateUrlHistory3->setAffiliateProject($affiliateProject);
        $affiliateUrlHistory3->setSurveyUrl($surveyUrl);
        $affiliateUrlHistory3->setStatus($status3);

        $status4 = AffiliateUrlHistory::SURVEY_STATUS_QUOTAFULL;
        $affiliateUrlHistory4 = new AffiliateUrlHistory();
        $affiliateUrlHistory4->setUKey($uKey);
        $affiliateUrlHistory4->setAffiliateProject($affiliateProject);
        $affiliateUrlHistory4->setSurveyUrl($surveyUrl);
        $affiliateUrlHistory4->setStatus($status4);

        $status5 = AffiliateUrlHistory::SURVEY_STATUS_ERROR;
        $affiliateUrlHistory5 = new AffiliateUrlHistory();
        $affiliateUrlHistory5->setUKey($uKey);
        $affiliateUrlHistory5->setAffiliateProject($affiliateProject);
        $affiliateUrlHistory5->setSurveyUrl($surveyUrl);
        $affiliateUrlHistory5->setStatus($status5);

        $this->em->persist($affiliateUrlHistory1);
        $this->em->persist($affiliateUrlHistory2);
        $this->em->persist($affiliateUrlHistory3);
        $this->em->persist($affiliateUrlHistory4);
        $this->em->persist($affiliateUrlHistory5);
        $this->em->flush();

        $url = $this->affiliateSurveyService->getSurveyUrl($affiliateProject->getId());

        $this->assertEquals(null, $url);
        
    }

    /**
    */
    public function testGetSurveyUrl_tableNotExist(){
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $uKey = '09901562asarccm88ui8';
        $projectId = 1;
        $surveyUrl  = "http://r.researchpanelasia.com.dev1.researchpanelasia.com/redirect/forward/784/1562/09901562asarccm88ui8";
        $status = AffiliateUrlHistory::SURVEY_STATUS_INIT;

        $url = $this->affiliateSurveyService->getSurveyUrl($projectId);

        // 检查url是否被正确的取到
        $this->assertEquals(null, $url);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }
}
