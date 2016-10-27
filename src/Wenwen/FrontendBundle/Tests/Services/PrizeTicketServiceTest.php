<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadPrizeItemData;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\PrizeItem;

class PrizeTicketServiceTest extends WebTestCase
{
    private $em;
    private $prizeTicketService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadUserData());
        $loader->addFixture(new LoadPrizeItemData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->prizeTicketService = $container->get('app.prize_ticket_service');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testPrizeTicket()
    {
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');

        $ticket = $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, '大');
        $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_SMALL, '小');
        $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_SMALL);
        $this->assertEquals(3, count($this->prizeTicketService->getUnusedPrizeTickets($user)));

        $this->prizeTicketService->deletePrizeTicket($ticket);
        $this->assertEquals(2, count($this->prizeTicketService->getUnusedPrizeTickets($user)));
    }
}
