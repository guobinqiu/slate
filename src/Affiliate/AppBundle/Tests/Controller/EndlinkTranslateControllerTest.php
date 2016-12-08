<?php
namespace Affiliate\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Affiliate\AppBundle\Controller\EndlinkTranslateController;


class EndlinkTranslateControllerTest extends WebTestCase
{

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testTransferEndlink(){

        $endlinkTranslateController = new EndlinkTranslateController();

        $status = 'complete';
        $prj = 'sdfsdfhl23423l4';
        $uid = '3434sdf';

        $redirectUrl = $endlinkTranslateController->transferEndlink($status, $prj, $uid);

        echo PHP_EOL . $redirectUrl ;

    }
}
