<?php
namespace Jili\ApiBundle\DataFixtures\ORM\MarketActivity;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\Advertiserment;

/**
 * 
 **/
class LoadAdvertisermentCodeData extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
   public static  $ROWS;


    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS= array();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
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

        $row = new Advertiserment();
        $row->setType('3,4,5,6,7');
        $row->setTitle('G2000天猫店');
        $row->setCreatedTime(new \Datetime('2013-07-24 09:30:39'));
        $row->setStartTime(new \Datetime('2013-05-02 00:00:00'));
        $row->setEndTime(new \Datetime('2015-07-09 00:00:00'));
        $row->setUpdateTime( new \Datetime('2013-07-24 09:30:39'));
        $row->setDecription('G2000天猫店G2000天猫店G2000天猫店');
        $row->setContent('成功购买无退货 【数据返回方式】隔天<br />');
        $row->setImageurl('http://count.chanet.com.cn/click.cgi?a=480534&d=22815&u=&e=');
        $row->setIconImage('images/actionPic/1374629439_8683.jpeg');
        $row->setListImage('images/actionPic/1374629439_2701.jpeg');
        $row->setIncentiveType( 2);
        $row->setIncentiveRate( 200);
        $row->setRewardRate( 30);
        $row->setIncentive(210);
        $row->setInfo(  'G2000品牌于1985年创立（名称源自纵横二千集团前英文名称Generation&nbsp; 2000.Li&nbsp; mited），并于市场定位为专业服装连锁店，全力销售时尚潮流的男仕及女仕上班服。G2000一贯的设计概念，是选用高质布料演绎最新欧洲时装潮流，旨在为都市人供应无尽的衣着配搭，让他们配合自己的生活品味，自信地生活在都市生活中。G2000天猫店为G2000的官方旗舰店。&nbsp; <br />');

        $row->setCategory( 2);
        $row->setDeleteFlag( 0);

        $manager->persist($row);
        $manager->flush();

        self::$ROWS[] = $row;
        $this->addReference('advertiserment0', $row);
    }
}

