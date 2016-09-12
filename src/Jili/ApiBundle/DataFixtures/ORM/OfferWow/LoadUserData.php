<?php
namespace Jili\ApiBundle\DataFixtures\ORM\OfferWow;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Wenwen\FrontendBundle\Entity\User;

class LoadUserData extends  AbstractFixture implements   FixtureInterface, ContainerAwareInterface
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
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $user->setPwd('123qwe');

        $manager->persist($user);
        $manager->flush();
        self::$ROWS [] = $user;
    }
}
?>
