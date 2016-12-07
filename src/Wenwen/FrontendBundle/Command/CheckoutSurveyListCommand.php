<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Model\SurveyStatus;

class CheckoutSurveyListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sop:checkout_survey_list');
        $this->setDescription('把api返回的surveylist数据同步到3张问卷表');
        $this->addOption('user_id', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $surveyService = $this->getContainer()->get('app.survey_service');
        $surveySopService = $this->getContainer()->get('app.survey_sop_service');
        $surveyFulcrumService = $this->getContainer()->get('app.survey_fulcrum_service');
        $surveyCintService = $this->getContainer()->get('app.survey_cint_service');

        $userId = $input->getOption('user_id');
        $appMid = $surveyService->getSopRespondentId($userId);
        $result = $surveyService->getSopSurveyListJson($userId);
        //$result = $surveyService->getDummySurveyListJson();
        $output->writeln('result=' . $result);

        if (empty($result)) {
            throw new \Exception('empty survey list');
        }

        $sop = json_decode($result, true);

        if ($sop['meta']['code'] != 200) {
            throw new \Exception($sop['meta']['message']);
        }

        foreach ($sop['data']['research'] as $survey) {
            $surveySopService->createOrUpdateSurvey($survey);
            $surveySopService->createParticipationHistory($appMid, $survey['survey_id'], SurveyStatus::STATUS_TARGETED);
        }

        foreach ($sop['data']['fulcrum_research'] as $survey) {
            $surveyFulcrumService->createOrUpdateSurvey($survey);
            $surveyFulcrumService->createParticipationHistory($appMid, $survey['survey_id'], SurveyStatus::STATUS_TARGETED);
        }

        foreach ($sop['data']['cint_research'] as $survey) {
            $surveyCintService->createOrUpdateSurvey($survey);
            $surveyCintService->createParticipationHistory($appMid, $survey['survey_id'], SurveyStatus::STATUS_TARGETED);
        }
    }
}
