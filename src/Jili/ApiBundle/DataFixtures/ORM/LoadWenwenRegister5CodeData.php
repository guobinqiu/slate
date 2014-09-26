<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;

class LoadWenwenRegister5CodeData  extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface
{
    static public $ROWS;
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
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setNick('zhangmm');
        $user->setEmail('zhangmm@voyagegroup.com.cn');
        $user->setIsFromWenwen(2);
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $manager->persist($user);
        $manager->flush();

        self::$ROWS[] = $user;

        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user->getId());
        $str = 'jilifirstregister';
        $code = md5($user->getId().str_shuffle($str));
        $setPasswordCode->setCode($code);
        $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
        $manager->persist($setPasswordCode);
        $manager->flush();
    }
}
