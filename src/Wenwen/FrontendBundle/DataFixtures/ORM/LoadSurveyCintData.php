<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\SurveyCint;

class LoadSurveyCintData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $survey = new SurveyCint();
        $survey->setSurveyId(10000);
        $survey->setQuotaId(46737);
        $survey->setLoi(20);
        $survey->setIr(0);
        $survey->setCpi(0);
        $survey->setTitle("cint research survey1");
        $survey->setCompletePoint(400);
        $survey->setScreenoutPoint(2);
        $survey->setQuotafullPoint(1);
        $survey->setStartDate(new \DateTime());
        $survey->setEndDate(new \DateTime());
        $survey->setComment(null);
        $survey->setPcBlocked(0);
        $survey->setMobileBlocked(1);
        $survey->setTabletBlocked(1);
        $survey->setIsClosed(0);
        $survey->setIsFixedLoi(1);
        $survey->setIsNotifiable(1);
        $manager->persist($survey);
        $manager->flush();

        $survey = new SurveyCint();
        $survey->setSurveyId(10001);
        $survey->setQuotaId(46738);
        $survey->setLoi(20);
        $survey->setIr(0);
        $survey->setCpi(0);
        $survey->setTitle("cint research survey2");
        $survey->setCompletePoint(400);
        $survey->setScreenoutPoint(2);
        $survey->setQuotafullPoint(1);
        $survey->setStartDate(new \DateTime());
        $survey->setEndDate(new \DateTime());
        $survey->setComment(null);
        $survey->setPcBlocked(0);
        $survey->setMobileBlocked(1);
        $survey->setTabletBlocked(1);
        $survey->setIsClosed(0);
        $survey->setIsFixedLoi(0);
        $survey->setIsNotifiable(0);
        $manager->persist($survey);
        $manager->flush();
    }
}