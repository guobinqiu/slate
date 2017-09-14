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
        $logger = $this->getContainer()->get('logger');

        $userId = $input->getOption('user_id');
        $result = $surveyService->getSopSurveyListJson($userId);
        //$result = $surveyService->getDummySurveyListJson();//读取测试数据
        $output->writeln('result=' . $result);

        if (empty($result)) {
            $logger->warn(__METHOD__ . ' empty survey list');
            return;
        }

        $sop = json_decode($result, true);

        if ($sop['meta']['code'] != 200) {
            $logger->debug(__METHOD__ . ' ' . $sop['meta']['message']);
            return;
        }

        foreach ($sop['data']['research'] as $survey) {
            $surveySopService->createOrUpdateSurvey($survey);
            $surveySopService->createParticipationByUserId($userId, $survey['survey_id'], SurveyStatus::STATUS_TARGETED);
        }

        foreach ($sop['data']['fulcrum_research'] as $survey) {
            $surveyFulcrumService->createOrUpdateSurvey($survey);
            $surveyFulcrumService->createParticipationByUserId($userId, $survey['survey_id'], SurveyStatus::STATUS_TARGETED);
        }

        foreach ($sop['data']['cint_research'] as $survey) {
            $surveyCintService->createOrUpdateSurvey($survey);
            $surveyCintService->createParticipationByUserId($userId, $survey['survey_id'], SurveyStatus::STATUS_TARGETED);
        }
    }
}
