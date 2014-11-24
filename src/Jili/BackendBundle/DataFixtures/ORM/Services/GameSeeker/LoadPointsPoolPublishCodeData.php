<?php
namespace Jili\BackendBundle\DataFixtures\ORM\Services\GameSeeker;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\BackendBundle\Entity\GameSeekerPointsPool;

class LoadPointsPoolPublishCodeData  extends AbstractFixture implements FixtureInterface
{

    // sql file ?
//    insert data 
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        // $manager->createQuery('insert into Jili\BackendBundle\Entity\GameSeekerPointsPool ( ) values () ');
        $fixtures = array(
            '1000','0',
            '1000','1',
            '500','2',
            '200','5',
            '1','500',
        );
        $records = array();
        $createdAt = new \Datetime();
        foreach($fixtures as $k => $v) {
            $columns = explode(':' , $v);
            $entity = new GameSeekerPointsPool();
            $entity->setCreatedAt($createdAt)
            ->setUpdatedAt($createdAt)
            ->setPoints($v)
            ->setSendFrequency( $k)
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

