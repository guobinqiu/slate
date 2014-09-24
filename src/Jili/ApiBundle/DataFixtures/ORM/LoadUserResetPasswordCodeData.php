<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;

class LoadUserResetPasswordCodeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface
{
    public static $ROWS;
    public static $SET_PASSWORD_CODE ;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS = array();
        self::$SET_PASSWORD_CODE = array();
    }
    /**
    * {@inheritDoc}
    */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice.nima@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();

        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user->getId());
        $str = 'jilifirstregister';
        $code = md5($user->getId().str_shuffle($str));
        $setPasswordCode->setCode($code);
        $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
        $manager->persist($setPasswordCode);
        $manager->flush();

        self::$ROWS[] = $user;
        self::$SET_PASSWORD_CODE[] = $setPasswordCode;

    }

}
