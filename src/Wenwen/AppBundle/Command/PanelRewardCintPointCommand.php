<?php

namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\CintResearchSurveyParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;

class PanelRewardCintPointCommand extends PanelRewardCommand
{
    const POINT_TYPE_COST = 11;

    protected function configure()
    {
        $this->setName('panel:reward-cint-point')->setDescription('request SOP API and reward points based on retrived data')->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start panel:reward-cint-point: '.date('Y-m-d H:i:s'));
        $app_name = 'site91wenwen';
        $this->setLogger($app_name . '-reward-cint-point');
        return parent::execute($input, $output);
    }

    protected function point($history)
    {
        return $history['extra_info']['point'];
    }

    protected function type($history)
    {
        return CategoryType::CINT_COST;
    }

    protected function task($history)
    {
        return TaskType::SURVEY;
    }

    protected function comment($history)
    {
        // title is saved in extra_info.title for old history
        $title = isset($history['title']) ? $history['title'] : $history['extra_info']['title'];
        return 'c' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        $sop_configure = $this->getContainer()->getParameter('sop');
        return $sop_configure['api_v1_1_cint_surveys_research_participation_history'];
    }

    protected function requiredFields()
    {
        return array (
            'response_id',
            'yyyymm',
            'app_id',
            'app_mid',
            'survey_id',
            'quota_id',
            'title',
            'loi',
            'cpi',
            'answer_status',
            'extra_info'
        );
    }

    protected function skipReward($history)
    {
        if(self::POINT_TYPE_COST != $history['extra_info']['point_type']){
            // Cint 只有 11(商业问卷 cost) 类型，除此之外不处理
            return true;
        }
        return false;
    }

    protected function skipRewardAlreadyExisted($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userId = $this->getContainer()->get('app.user_service')->toUserId($history['app_mid']);
        $participations = $em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findBy(array(
            //'appMid' => $history['app_mid'],
            'surveyId' => $history['survey_id'],
            'userId' => $userId,
        ));
        if (count($participations) < 3) {
            throw new \Exception('菲律宾那边有误操作，没回答过的用户也撒钱，钱多是吗？');
        }
        if (count($participations) == 4) {
            foreach ($participations as $participation) {
                if (in_array($participation->getStatus(), SurveyStatus::$answerStatuses)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function createParticipationHistory($history)
    {
        return $this->getContainer()->get('app.survey_cint_service')->createParticipationByAppMid(
            $history['app_mid'],
            $history['survey_id'],
            $history['answer_status']
        );
    }

    protected function getPanelType() {
        return 'Cint';
    }
}
