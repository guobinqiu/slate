<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\FulcrumResearchSurvey;

class LoadFulcrumResearchSurveyData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $researchSurvey = new FulcrumResearchSurvey();
        $researchSurvey->setSurveyId(10000);
        $researchSurvey->setQuotaId(46737);
        $researchSurvey->setLoi(20);
        $researchSurvey->setIr(0);
        $researchSurvey->setCpi(0);
        $researchSurvey->setTitle("fulcrum research survey");
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
    }
}