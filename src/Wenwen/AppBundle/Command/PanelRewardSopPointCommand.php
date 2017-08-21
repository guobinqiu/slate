<?php

namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;

class PanelRewardSopPointCommand extends PanelRewardCommand
{
    const POINT_TYPE_COST = 11;
    const POINT_TYPE_EXPENSE = 61;

    protected function configure()
    {
        $this->setName('panel:reward-sop-point')
                ->setDescription('request SOP API and reward points based on retrived data')
                ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
                ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }

    protected function point($history)
    {
        return $history['extra_info']['point'];
    }

    protected function type($history)
    {
        if(self::POINT_TYPE_EXPENSE == $history['extra_info']['point_type']){
            return CategoryType::SOP_EXPENSE;
        } elseif(self::POINT_TYPE_COST == $history['extra_info']['point_type']){
            return CategoryType::SOP_COST;
        }
    }

    protected function task($history)
    {
        if(self::POINT_TYPE_EXPENSE == $history['extra_info']['point_type']){
            return TaskType::RENTENTION;
        } elseif(self::POINT_TYPE_COST == $history['extra_info']['point_type']){
            return TaskType::SURVEY;
        }
    }

    protected function answerStatus($history){
        return strtolower($history['answer_status']);
    }

    protected function approvalStatus($history){
        return strtolower($history['approval_status']);
    }

    protected function comment($history)
    {
        // title is saved in extra_info.title for old history
        $title = isset($history['title']) ? $history['title'] : $history['extra_info']['title'];
        return 'r' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        $sopConfig = $this->getContainer()->getParameter('sop');
        return $sopConfig['api_v1_1_surveys_research_participation_history'];
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
            'approval_status',
            'approved_at',
            'extra_info'
        );
    }

    protected function skipReward($history)
    {
        if(self::POINT_TYPE_EXPENSE == $history['extra_info']['point_type']){

        } elseif(self::POINT_TYPE_COST == $history['extra_info']['point_type']){

        } else {
            // 据SOP的API说，肯定没有 11 和 61之外的值
            // 所以，当point_type 不是11 or 61时，skip
            return true;
        }
        return false;
    }

    protected function skipRewardAlreadyExisted($history)
    {
        try {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $user = $this->getContainer()->get('app.user_service')->getUserBySopRespondentAppMid($history['app_mid']);
            $participations = $em->getRepository('WenwenFrontendBundle:SurveySopParticipationHistory')->findBy(array(
                //'appMid' => $history['app_mid'],
                'surveyId' => $history['survey_id'],
                'userId' => $user->getId(),
            ));
            if (count($participations) < 3 || count($participations) > 4) {
                $this->logger->error('Only 3 (targeted, init and forward) or 4 (targeted, init, forward and one of C/S/Q/E) is valid.');
                return true;
            }
            if (count($participations) == 4) {
                foreach ($participations as $participation) {
                    if (in_array($participation->getStatus(), SurveyStatus::$answerStatuses)) {
                        return true;
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' ' . $e->getMessage());
            return true;
        }
    }

    protected function createParticipationHistory($history)
    {
        return $this->getContainer()->get('app.survey_sop_service')->createParticipationByAppMid(
            $history['app_mid'],
            $history['survey_id'],
            $history['answer_status']
        );
    }

    protected function getPanelType() {
        return 'SOP';
    }

    protected function preHandle(array $history_list) {
        if (!empty($history_list)) {
            $history = $history_list[0];
            $em = $this->getContainer()->get('doctrine')->getManager();
            $survey = $em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => $history['survey_id']));
            if ($survey != null && $survey->getPointType() == null) {
                if (isset($history['extra_info']['point_type'])) {
                    $survey->setPointType($history['extra_info']['point_type']);
                    $em->flush($survey);
                }
            }
        }
    }
}
