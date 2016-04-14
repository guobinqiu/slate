<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;

class LoadUserLandingWenwenCodeData  extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
    static public $ROWS;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS = array();
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
        $user->setNick('alice32');
        $user->setEmail('alice.nima@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $user->setIsEmailConfirmed(1);

        $user->setPwd('aaaaaa');
        $manager->persist($user);
        $manager->flush();

        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user->getId());

        $str = 'jilifirstregister';
        $code = md5($user->getId().str_shuffle($str));
        $setPasswordCode->setCode($code);
        $invalid_created =new \DateTime();
        $invalid_created ->setTimestamp( time() - SetPasswordCode::$VALIDATION_OF_SIGNUP_ACTIVATE -1  );
        $setPasswordCode->setCreateTime($invalid_created );
        $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
        ######
        $manager->persist($setPasswordCode);
        $manager->flush();
        self::$ROWS[] = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
