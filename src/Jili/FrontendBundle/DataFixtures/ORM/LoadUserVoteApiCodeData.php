<?php
namespace Jili\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;

class LoadUserVoteApiCodeData  extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
    static public $USER ;
    public static $SET_PASSWORD_CODE ;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$USER = array();
        self::$SET_PASSWORD_CODE = array();
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
        $user->setNick('chiang32');
        $user->setEmail('chiangtor@gmail.com');
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

//        $this->addReference('user0', $user);
 //       $this->addReference('set_password_code0', $setPasswordCode);
        self::$USER[] = $user;
        self::$SET_PASSWORD_CODE[] = $setPasswordCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
