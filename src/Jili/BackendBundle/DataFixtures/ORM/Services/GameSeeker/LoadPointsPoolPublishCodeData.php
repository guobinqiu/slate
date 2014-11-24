<?php
namespace Jili\BackendBundle\DataFixtures\ORM\Services\GameSeeker;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\BackendBundle\Entity\GameSeekerPointsPool;

class LoadPointsPoolPublishCodeData  extends AbstractFixture implements FixtureInterface
{

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        // $manager->createQuery('insert into Jili\BackendBundle\Entity\GameSeekerPointsPool ( ) values () ');
        $fixtures = array(
            array('1000','0'),
            array('1000','1'),
            array('500','2'),
            array('200','5'),
            array('1','500'),
        );
        $records = array();
        $createdAt = new \Datetime();

        foreach($fixtures as $k => $v) {
            $f= $v[0];
            $pts =$v[1]; 
            $entity = new GameSeekerPointsPool();
            $entity->setCreatedAt($createdAt)
            ->setUpdatedAt($createdAt)
            ->setPoints( (string) $pts )
            ->setSendFrequency( $f)
            ->setIsPublished(1)
            ->setPublishedAt($createdAt)
            ->setIsValid(1);
            $manager->persist($entity);
            $records[]= $entity;
        }

        $manager->flush(); 
        $manager->clear(); // Detaches all objects from Doctrine!
        return $records;
    }
}

