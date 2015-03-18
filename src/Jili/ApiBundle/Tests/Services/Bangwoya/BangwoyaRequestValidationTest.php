<?php
namespace  Jili\ApiBundle\Tests\Services\Bangwoya;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\BangwoyaOrder;

class BangwoyaRequestValidationTest extends KernelTestCase {

    /**
     * @group issue_578
     */
    public function testValidate() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        $config = $container->getParameter('bangwoya_com');

        // insert user
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $em->persist($user);
        $em->flush();

        $tid = '1';
        $partnerid = $user->getId();
        $vmoney = 100;
        $nonceStr = md5($config['key'] . $partnerid . $vmoney . $tid);
        $clientIp = $config['client_ip'];

        $request_validator = $container->get('bangwoya_request.validation');

        //验证通过
        $validate_return = $request_validator->validate($tid, $partnerid, $vmoney, $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":true,"code":""}', json_encode($validate_return));

        //1.缺少参数
        $validate_return = $request_validator->validate($tid, null, $vmoney, $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":false,"code":"1001"}', json_encode($validate_return));

        //2.密码验证不通过
        $validate_return = $request_validator->validate($tid, $partnerid, '200', $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":false,"code":"1002"}', json_encode($validate_return));

        //3.tid重复
        $order = new BangwoyaOrder();
        $order->setUserid($partnerid);
        $order->setTid($tid);
        $order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
        $order->setDeleteFlag(0);
        $em->persist($order);
        $em->flush();
        $validate_return = $request_validator->validate($tid, $partnerid, $vmoney, $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":false,"code":"1003"}', json_encode($validate_return));

        //4.vmoney超过最大限额
        $tid = '2';
        $vmoney = 5001;
        $nonceStr = md5($config['key'] . $partnerid . $vmoney . $tid);
        $validate_return = $request_validator->validate($tid, $partnerid, '5001', $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":false,"code":"1004"}', json_encode($validate_return));

        //5.partnerid不存在
        $partnerid = 1000000;
        $vmoney = 100;
        $nonceStr = md5($config['key'] . $partnerid . $vmoney . $tid);
        $validate_return = $request_validator->validate($tid, $partnerid, $vmoney, $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":false,"code":"1005"}', json_encode($validate_return));

        //6.非法IP 程序里获得ip地址的地方
        $partnerid = $user->getId();
        $clientIp = '11.11.11.11';
        $nonceStr = md5($config['key'] . $partnerid . $vmoney . $tid);
        $validate_return = $request_validator->validate($tid, $partnerid, $vmoney, $nonceStr, $clientIp);
        $this->assertEquals('{"valid_flag":false,"code":"1006"}', json_encode($validate_return));
    }
}
