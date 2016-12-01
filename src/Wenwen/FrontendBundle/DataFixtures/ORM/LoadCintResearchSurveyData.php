<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\CintResearchSurvey;

class LoadCintResearchSurveyData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $researchSurvey = new CintResearchSurvey();
        $researchSurvey->setSurveyId(10000);
        $researchSurvey->setQuotaId(46737);
        $researchSurvey->setLoi(20);
        $researchSurvey->setIr(0);
        $researchSurvey->setCpi(0);
        $researchSurvey->setTitle("cint research survey1");
        $researchSurvey->setCompletePoint(400);
        $researchSurvey->setScreenoutPoint(2);
        $researchSurvey->setQuotafullPoint(1);
        $researchSurvey->setStartDate(new \DateTime());
        $researchSurvey->setEndDate(new \DateTime());
        $researchSurvey->setComment(null);
        $researchSurvey->setPcBlocked(0);
        $researchSurvey->setMobileBlocked(1);
        $researchSurvey->setTabletBlocked(1);
        $researchSurvey->setIsClosed(0);
        $researchSurvey->setIsFixedLoi(1);
        $researchSurvey->setIsNotifiable(1);
        $manager->persist($researchSurvey);
        $manager->flush();

        $researchSurvey = new CintResearchSurvey();
        $researchSurvey->setSurveyId(10001);
        $researchSurvey->setQuotaId(46738);
        $researchSurvey->setLoi(20);
        $researchSurvey->setIr(0);
        $researchSurvey->setCpi(0);
        $researchSurvey->setTitle("cint research survey2");
        $researchSurvey->setCompletePoint(400);
        $researchSurvey->setScreenoutPoint(2);
        $researchSurvey->setQuotafullPoint(1);
        $researchSurvey->setStartDate(new \DateTime());
        $researchSurvey->setEndDate(new \DateTime());
        $researchSurvey->setComment(null);
        $researchSurvey->setPcBlocked(0);
        $researchSurvey->setMobileBlocked(1);
        $researchSurvey->setTabletBlocked(1);
        $researchSurvey->setIsClosed(0);
        $researchSurvey->setIsFixedLoi(0);
        $researchSurvey->setIsNotifiable(0);
        $manager->persist($researchSurvey);
        $manager->flush();
    }
}