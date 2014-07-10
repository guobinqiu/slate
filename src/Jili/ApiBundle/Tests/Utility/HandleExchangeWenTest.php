<?php
namespace Jili\ApiBundle\Tests\Utility;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\ApiBundle\Controller\AdminController;

class HandleExchangeWenTest extends WebTestCase {

    public function testHandleExchangeWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        //测试有关表user,exchange_from_wenwen
        $file[1][0] = "91jili-201402-2624-927390";
        $file[1][1] = "zhangmm@voyagegroup.com.cn";
        $file[1][2] = "30";
        $file[1][3] = "3000";

        $file[2][0] = "91jili-201402-2625-1036110";
        $file[2][1] = "zhangmm1@voyagegroup.com.cn";
        $file[2][2] = "30";
        $file[2][3] = "3000";

        $file[3][0] = "91jili-201402-2625-1036111";
        $file[3][1] = "zhangmm2@voyagegroup.com.cn";
        $file[3][2] = "30";
        $file[3][3] = "3000";

        $return = $controller->handleExchangeWen($file);
        $this->assertEquals(2, count($return));
    }
}