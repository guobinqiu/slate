<?php
namespace Affiliate\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliatePartner;



class AffiliateSurveyControllerTest extends WebTestCase
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
    public function testShowEndpageAction_withoutStatusAnduKey(){
        $client = static::createClient();
        $url = static::$kernel->getContainer()->get('router')->generate('affiliate_endpage');

        $crawler = $client->request('GET', $url, array ());
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(00, $client->getContainer()->get('session')->get('complete_point'));
    }

    /**
    */
    public function testShowEndpageAction_withStatusAnduKey(){
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
        $point = 300;
        
        $uKey = '09901562asarccm88ui8';
        $surveyUrl  = "http://r.researchpanelasia.com.dev1.researchpanelasia.com/redirect/forward/784/1562/09901562asarccm88ui8";
        $urlStatus = AffiliateUrlHistory::SURVEY_STATUS_FORWARD;

        $affiliateProject = new AffiliateProject();
        $affiliateProject->setAffiliatePartner($affiliatePartner);
        $affiliateProject->setRFQId($RFQId);
        $affiliateProject->setOriginalFileName($originalFileName);
        $affiliateProject->setRealFullPath($realFullPath);
        $affiliateProject->setInitNum(1);
        $affiliateProject->setCompletePoints($point);

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
 

        $client = static::createClient();
        $url = static::$kernel->getContainer()->get('router')->generate('affiliate_endpage');

        $crawler = $client->request('GET', $url, array (
            'status' => AffiliateUrlHistory::SURVEY_STATUS_COMPLETE,
            'uniq_key' => $uKey,
            ));
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // 检查session里complete_point有没有被设置
        $this->assertEquals($point, $client->getContainer()->get('session')->get('complete_point'));
    }
}
