<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Controller\ExchangeController;
use Jili\ApiBundle\Entity\PointsExchange;

class ExchangeControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group check_birthday
     */
    public function testBirthdayIsValid()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $controller = new ExchangeController();
        $controller->setContainer($container);

        $today = date("Ymd", time());
        $yesterday = date("Ymd", strtotime("-1 day"));
        $tomorrow = date("Ymd", strtotime("+1 day"));
        $valid_date = date("Ym", strtotime("-1 day")) . '32';

        $identityCard = '110912' . $tomorrow . '3734';
        $return = $controller::birthdayIsValid($identityCard);
        $this->assertFalse($return, 'birthday is after today ' . $identityCard);

        $identityCard = '110912' . $valid_date . '3734';
        $return = $controller::birthdayIsValid($identityCard);
        $this->assertFalse($return, 'birthday is invalid ' . $identityCard);

        $identityCard = '110912' . $today . '3734';
        $return = $controller::birthdayIsValid($identityCard);
        $this->assertTrue($return, 'birthday is today ' . $identityCard);

        $identityCard = '110912' . $yesterday . '3734';
        $return = $controller::birthdayIsValid($identityCard);
        $this->assertTrue($return, 'birthday is valid ' . $identityCard);
    }
}