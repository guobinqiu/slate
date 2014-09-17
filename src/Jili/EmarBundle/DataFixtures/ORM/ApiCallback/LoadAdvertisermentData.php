<?php

namespace Jili\EmarBundle\DataFixtures\ORM\ApiCallback;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\Advertiserment;

class LoadAdvertisermentData extends AbstractFixture implements  FixtureInterface,ContainerAwareInterface,OrderedFixtureInterface
{


    public static $ROWS;
    
    function __construct()
    {
        self::$ROWS = array(); 
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) 
    {
        $ad = new Advertiserment();


        $ad->setTitle('苏宁易购');
        $ad->setCreatedTime(new \DateTime('2013-08-01 11:04:55'));
        $ad->setStartTime(new \DateTime('2009-12-10 00:00:00'));
        $ad->setEndTime(new \DateTime('2014-06-07 00:00:00'));
        $ad->setUpdateTime(new \DateTime('2013-08-01 11:04:55'));
        $ad->setDecription('苏宁易购');
        $ad->setContent('<div>苏宁易购（www.suning.cn）是苏宁电器的网上商城，提供彩电、冰箱、洗衣机、空调、手机,笔记本电脑,数码相机,单反相机,MP3，MP4，厨房家电,厨卫家电,生活小电器,办公家电,家居用品的网上销售，是中国3C家电连锁零售企业领跑者。</div>');
        $ad->setImageurl('http://count.chanet.com.cn/click.cgi?a=480534&d=351169&u=&e='); 
        $ad->setIconImage('images/actionPic/1375326295_2330.jpeg');
        $ad->setListImage('images/actionPic/1375326295_6491.jpeg');
        $ad->setIncentiveType(2);
        $ad->setIncentiveRate( 8000);
        $ad->setRewardRate(30);
        $ad->setIncentive(2400);
        $ad->setInfo('<span style="line-height:1.5;">成功购买苏宁易购商品，并没有发生退货。</span><div>结算销售额为订单总金额扣除缺货商品金额、退货商品金额、运送费用、礼券费用的净值。</div>
<div>数据ad方式：隔日返回订单。</div>');
        $ad->setCategory(2);
        $ad->setDeleteFlag(0);
        $manager->persist($ad);
        $manager->flush();
        self::$ROWS [] = $ad;
    }

}
/*
            id: 83
          type: NULL
         title:    苏宁易购   
     action_id: NULL
  created_time: 2013-08-01 11:04:55
    start_time: 2009-12-10 00:00:00
      end_time: 2014-06-07 00:00:00
   update_time: 2013-08-01 11:04:55
    decription:    苏宁易购   
       content:    <div>苏宁易购（www.suning.cn）是苏宁电器的网上商城，提供彩电、冰箱、洗衣机、空调、手机,笔记本电脑,数码相机,单反相机,MP3，MP4，厨房家电,厨卫家电,生活小电器,办公家电,家居用品的网上销售，是中国3C家电连锁零售企业领跑者。</div>
   
      imageurl:    http://count.chanet.com.cn/click.cgi?a=480534&d=351169&u=&e=   
    icon_image: images/actionPic/1375326295_2330.jpeg
    list_image: images/actionPic/1375326295_6491.jpeg
incentive_type: 2
incentive_rate: 8000
   reward_rate: 30
     incentive: 2400
          info:   <span style="line-height:1.5;">成功购买苏宁易购商品，并没有发生退货。</span><div>结算销售额为订单总金额扣除缺货商品金额、退货商品金额、运送费用、礼券费用的净值。</div>
<div>数据返回方式：隔日返回订单。</div>
  
      category: 2
   delete_flag: 1
   wenwen_user: NULL
*/
