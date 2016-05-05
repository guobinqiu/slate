<?php
namespace Jili\ApiBundle\Tests\Command\Worker;

use Jili\ApiBundle\Command\Worker\ResearchSurveyDeliveryNotificationCommand;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use JMS\JobQueueBundle\Entity\Job;

class ResearchSurveyDeliveryNotificationCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
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

    public function testExecute() 
    {
        // mock the Kernel or create one depending on your needs
        $em = $this->em;
        $application = new Application(static::$kernel);
        $application->add(new ResearchSurveyDeliveryNotificationCommand());

        $data1 = array (
            'name1' => 'Test1',
            'email' => 'miaomiao.zhang@d8aspring.com',
            'title' => '先生',
            'survey_title' => 'RPA Test Fulcrum Survey Delivery',
            'survey_point' => '101'
        );
        $data2 = array (
            'name1' => 'Test2',
            'email' => 'miaomiao.zhang+1@d8aspring.com',
            'title' => '先生',
            'survey_title' => 'RPA Test Fulcrum Survey Delivery',
            'survey_point' => '101'
        );

        $add_recipients[] = \Jili\ApiBundle\Utility\String::encodeForCommandArgument($data1);
        $add_recipients[] = \Jili\ApiBundle\Utility\String::encodeForCommandArgument($data2);

        $command = $application->find('research_survey:delivery_notification');
        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            '--campaign_id' => '23', # 91wenwen-survey-mailing2
            '--mailing_id' => '90004', # survey-mail-fulcrum
            '--group_name' => 'test_by_jarod',
            'recipients' => implode(' ', $add_recipients)
        );

        $commandTester->execute($commandParam);

    }

    /**
     * @group debug
     */
    public function testExecuteFromJob() 
    {
        $add_recipients = array( 'VyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMEBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF8wIiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMUBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF8xIiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMkBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF8yIiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrM0BnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF8zIiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNEBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF80Iiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNUBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF81Iiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNkBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF82Iiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrN0BnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF83Iiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOEBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF84Iiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOUBnbWFpbC5jb20iLCJ0aXRsZSI6InRlc3QiLCJzdXJ2ZXlfdGl0bGUiOiJzdXJ2ZXlfdGl0bGVfdGVzdF85Iiwic3VydmV5X3BvaW50IjoxMDF9',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMThAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMTlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMTkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjhAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMjlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMjkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzhAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrMzlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfMzkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDhAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNDlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNDkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNThAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNTlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNTkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjhAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNjlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNjkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzhAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrNzlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfNzkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODhAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrODlAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfODkiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTBAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTAiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTFAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTEiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTJAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTIiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTNAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTMiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTRAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTQiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTVAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTUiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTZAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTYiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOTdAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTciLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=',
            'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOThAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0='
        );
        $em = $this->em;
        $args = array( '--campaign_id=23',
            '--group_name=test_by_jarod',
            '--mailing_id=90004',
            'recipients='.implode(' ', $add_recipients)
        );

        $job = new Job('research_survey:delivery_notification', $args,  true, '91wenwen');
        $em->persist($job);
        $em->flush($job);
        $jobs =  $em->getRepository('JMSJobQueueBundle:Job')->findAll();
        $this->assertCount(1, $jobs, 'only 1 job ' );
        $job=$jobs[0];
        $this->assertEquals(Job::STATE_PENDING,$job->getState() ,'pending');
        $this->assertEquals('research_survey:delivery_notification',$job->getCommand() ,'the comand ');
        $this->assertEquals('91wenwen',$job->getQueue() ,'the queue');
        $this->assertEquals($args ,$job->getArgs() ,'pending');


    }
}
