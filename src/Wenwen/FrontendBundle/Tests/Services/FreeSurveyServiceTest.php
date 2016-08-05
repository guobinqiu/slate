<?php
namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Services\FreeSurveyService;
use Wenwen\FrontendBundle\Entity\FreeSurveyHistory;
use Wenwen\FrontendBundle\Entity\FreeProjectHistory;


class FreeSurveyServiceTest extends WebTestCase
{

    private $em;

    private $application;

    private $freeSurveyService;

    private $container;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = self::$kernel->getContainer();
        $this->freeSurveyService = static::$kernel->getContainer()->get('app.free_survey_service');
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
    public function testGetSurveyUrl_foundInit(){
        // 删掉所有表
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));

        // 测试结束，恢复所有表
        // 建立所有表
        //$this->runConsole("doctrine:schema:create");

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $uKey = '09901562asarccm88ui8';
        $partnerId = 1;
        $projectId = 1;
        $surveyUrl  = "http://r.researchpanelasia.com.dev1.researchpanelasia.com/redirect/forward/784/1562/09901562asarccm88ui8";
        //$surveyUrl  = "http09901562asarccm88ui8";
        $status = FreeSurveyService::SURVEY_STATUS_INIT;

        $freeSurveyHistory = new FreeSurveyHistory();
        $freeSurveyHistory->setUKey($uKey);
        $freeSurveyHistory->setPartnerId($partnerId);
        $freeSurveyHistory->setProjectId($projectId);
        $freeSurveyHistory->setSurveyUrl($surveyUrl);
        $freeSurveyHistory->setStatus($status);
        $this->em->persist($freeSurveyHistory);
        $this->em->flush();
        $id = $freeSurveyHistory->getId();

        $url = $this->freeSurveyService->getSurveyUrl($partnerId, $projectId);

        $this->assertEquals($surveyUrl, $url);

        $result = $this->em->getRepository('WenwenFrontendBundle:FreeSurveyHistory')->findOneById($id);
        $status = $result->getStatus();

        $this->assertEquals(FreeSurveyService::SURVEY_STATUS_FORWARD, $status);
    }

    /**
    */
    public function testGetSurveyUrl_notFound(){
        // 删掉所有表
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));


        // 测试结束，恢复所有表
        // 建立所有表
        //$this->runConsole("doctrine:schema:create");

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $uKey = '09901562asarccm88ui8';
        $partnerId = 1;
        $projectId = 1;
        $surveyUrl  = "http://r.researchpanelasia.com.dev1.researchpanelasia.com/redirect/forward/784/1562/09901562asarccm88ui8";
        //$surveyUrl  = "http09901562asarccm88ui8";
        $status1 = FreeSurveyService::SURVEY_STATUS_FORWARD;

        $freeSurveyHistory1 = new FreeSurveyHistory();
        $freeSurveyHistory1->setUKey($uKey);
        $freeSurveyHistory1->setPartnerId($partnerId);
        $freeSurveyHistory1->setProjectId($projectId);
        $freeSurveyHistory1->setSurveyUrl($surveyUrl);
        $freeSurveyHistory1->setStatus($status1);

        $status2 = FreeSurveyService::SURVEY_STATUS_COMPLETE;

        $freeSurveyHistory2 = new FreeSurveyHistory();
        $freeSurveyHistory2->setUKey($uKey);
        $freeSurveyHistory2->setPartnerId($partnerId);
        $freeSurveyHistory2->setProjectId($projectId);
        $freeSurveyHistory2->setSurveyUrl($surveyUrl);
        $freeSurveyHistory2->setStatus($status2);

        $status3 = FreeSurveyService::SURVEY_STATUS_SCREENOUT;

        $freeSurveyHistory3 = new FreeSurveyHistory();
        $freeSurveyHistory3->setUKey($uKey);
        $freeSurveyHistory3->setPartnerId($partnerId);
        $freeSurveyHistory3->setProjectId($projectId);
        $freeSurveyHistory3->setSurveyUrl($surveyUrl);
        $freeSurveyHistory3->setStatus($status3);

        $status4 = FreeSurveyService::SURVEY_STATUS_QUOTAFULL;

        $freeSurveyHistory4 = new FreeSurveyHistory();
        $freeSurveyHistory4->setUKey($uKey);
        $freeSurveyHistory4->setPartnerId($partnerId);
        $freeSurveyHistory4->setProjectId($projectId);
        $freeSurveyHistory4->setSurveyUrl($surveyUrl);
        $freeSurveyHistory4->setStatus($status4);

        $status5 = FreeSurveyService::SURVEY_STATUS_ERROR;

        $freeSurveyHistory5 = new FreeSurveyHistory();
        $freeSurveyHistory5->setUKey($uKey);
        $freeSurveyHistory5->setPartnerId($partnerId);
        $freeSurveyHistory5->setProjectId($projectId);
        $freeSurveyHistory5->setSurveyUrl($surveyUrl);
        $freeSurveyHistory5->setStatus($status5);


        $this->em->persist($freeSurveyHistory1);
        $this->em->persist($freeSurveyHistory2);
        $this->em->persist($freeSurveyHistory3);
        $this->em->persist($freeSurveyHistory4);
        $this->em->persist($freeSurveyHistory5);
        $this->em->flush();

        $url = $this->freeSurveyService->getSurveyUrl($partnerId, $projectId);

        $this->assertEquals(null, $url);
        
    }

    /**
    */
    public function testValidateProjectStatus_true(){

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $partnerId = 1;
        $projectId = 1;
        $status = FreeSurveyService::PROJECT_STATUS_OPEN;

        $freeProjectHistory = new FreeProjectHistory();
        $freeProjectHistory->setPartnerId($partnerId);
        $freeProjectHistory->setProjectId($projectId);
        $freeProjectHistory->setStatus($status);
        $this->em->persist($freeProjectHistory);
        $this->em->flush();

        $rtn = $this->freeSurveyService->validateProjectStatus($partnerId, $projectId);

        $this->assertEquals(true, $rtn);

    }

    /**
    */
    public function testValidateProjectStatus_false(){

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $partnerId1 = 1;
        $projectId1 = 1;
        $partnerId2 = 2;
        $projectId2 = 2;
        $status = FreeSurveyService::PROJECT_STATUS_CLOSE;

        for($i=1; $i<5; $i++){
            for($j=1; $j<10; $j++){
            $partnerId = $i;
            $projectId = $j;
            $freeProjectHistory = new FreeProjectHistory();
            $freeProjectHistory->setPartnerId($partnerId);
            $freeProjectHistory->setProjectId($projectId);
            $freeProjectHistory->setStatus($status);

            $this->em->persist($freeProjectHistory);
            $this->em->flush();
            }
        }

        $rtn1 = $this->freeSurveyService->validateProjectStatus($partnerId1, $projectId1);
        $rtn2 = $this->freeSurveyService->validateProjectStatus($partnerId2, $projectId2);

        $this->assertEquals(false, $rtn1);

    }


}
