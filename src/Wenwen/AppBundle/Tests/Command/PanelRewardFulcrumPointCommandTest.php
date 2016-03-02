<?php

namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\DataFixtures\ORM\LoadRewardFulcrumCommandData;

use Wenwen\AppBundle\Command\Wenwen\AppBundle\Command;
use Wenwen\AppBundle\Command\PanelRewardFulcrumPointCommand;

class PanelRewardFulcrumPointCommandTest extends KernelTestCase
{
  /**
   *      * @var \Doctrine\ORM\EntityManager
   *           */
  private $em;

  /**
   *      * {@inheritDoc}
   *           */
  public function setUp()
  {
    static::$kernel = static::createKernel(array (
      'environment' => 'test',
      'debug' => false
    ));
    static::$kernel->boot();
    $container = static::$kernel->getContainer();
    $em = $container->get('doctrine')->getManager();

    // purge tables
    $purger = new ORMPurger($em);
    $executor = new ORMExecutor($em, $purger);
    $executor->purge();
    // load fixtures
    $fixture = new LoadRewardFulcrumCommandData();
    $loader = new Loader();
    $loader->addFixture($fixture);
    $executor->execute($loader->getFixtures());


    $this->em = $em;
    $this->container = $container;
  }

  /**
   * {@inheritDoc}
   */
  protected function tearDown()
  {
    parent::tearDown();
    $this->em->close();
    Phake::resetStaticInfo();
  }


  public function testInvalidAppMID()
  {
    $em = $this->em;
    $container = $this->container;

    $application = new Application(static::$kernel);
    $application->add(new PanelRewardFulcrumPointCommand());
    $command = $application->find('panel:reward-fulcrum-point');
    $this->assertInstanceOf('\Wenwen\AppBundle\Command\PanelRewardFulcrumPointCommand', $command, 'wrong class type');
    $command->setContainer($container);
    $commandTester = new CommandTester($command);

    $sop_respondent = LoadRewardFulcrumCommandData::$SOP_RESPONDENT[0];

    $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
    $container->set('sop_api.client', $client);
    $app_mid = $sop_respondent->getId();

        // data
        $header = array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id', 'title',
                         'loi', 'ir', 'cpi', 'answer_status', 'approval_status','extra_info',);
        $rec1   = array('15','201502','2',$app_mid,'30001','30002', 'This is a title1',
                          '10','','1.500','SCREENOUT','APPROVED','{"point":"30","point_type":"11"}');
        $rec2   = array('16','201502','3','Invalid-app-mid','2','3', 'This is a title 2',
                          '11','','1.600','COMPLETE','APPROVED','{"point":"100","point_type":"11"}');
        $footer = array('EOF','Total 2 Records',);
        $response        = new \stdClass();
        $response->body  = array($header,$rec1,$rec2,$footer);

        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);

        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-03-02',
            '--definitive' => true
        );

        // execute
        try {
            $data = $commandTester->execute($commandParam);
        } catch (\Exception $e) {
            $this->assertEquals('No SopRespondent for: Invalid-app-mid', $e->getMessage());
        }

        // assert rollbacked
        $history = $em->getRepository('WenwenAppBundle:FulcrumResearchSurveyParticipationHistory')->findAll();
        $this->assertEmpty($history);

  }


  /**
   * @group debug
   */
  public function testUpdateTable()
  {
        // data
        $header = array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id', 'title',
                         'loi', 'ir', 'cpi', 'answer_status', 'approved_at','extra_info',);
        $rec1   = array('800001','201502','12','12','10001','10002', 'This is a title1',
                          '10','','1.500','COMPLETE','2015-02-14 06:00:06','{"point":"30","point_type":"11"}');
        $rec2   = array('800001','201502','12','12','20001','20002', 'This is a title2',
                          '11','','1.600','COMPLETE','2015-02-14 06:00:06','{"point":"100","point_type":"11"}');
        $rec3   = array('800001','201502','12','12','30001','30002', 'This is a title3',
                          '10','','1.500','SCREENOUT','2015-02-14 06:00:06','{"point":"0","point_type":"11"}');
        $rec4   = array('800001','201502','12','12','40001','40002', 'This is a title4',
                          '10','','1.500','QUOTAFULL','2015-02-14 06:00:06','{"point":"0","point_type":"11"}');
        $footer = array('EOF','Total 2 Records',);
        $response        = new stdClass();
        $response->body  = array($header,$rec1,$rec2,$rec3,$rec4,$footer);


}

  public static function getPointHistory()
  {}
}
?>
