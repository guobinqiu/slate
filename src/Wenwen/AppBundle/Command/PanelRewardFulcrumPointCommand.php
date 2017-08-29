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

class PanelRewardFulcrumPointCommand extends PanelRewardCommand
{
    const POINT_TYPE_COST = 11;

    protected function configure()
    {
      $this->setName('panel:reward-fulcrum-point')
        ->setDescription('request SOP API and reward points based on retrived data')
        ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
        ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db')
        ->addOption('resultNotification', null, InputOption::VALUE_NONE, 'If set, the task will send a notification to system team');
    }

    protected function point($history)
    {
        // Fulcrum 返回的所有状态的积分都是complete时的积分数
        // 这里根据状态来判断给多少积分
        $completePoint = $history['extra_info']['point'];
        $screenoutPoint = 20;
        $quotafullPoint = 20;
        $errorPoint = 0;
        if(SurveyStatus::STATUS_COMPLETE == $this->answerStatus($history)){
            return  $completePoint;
        } elseif(SurveyStatus::STATUS_SCREENOUT == $this->answerStatus($history)){
            return  $screenoutPoint;
        } elseif(SurveyStatus::STATUS_QUOTAFULL == $this->answerStatus($history)){
            return  $quotafullPoint;
        } else {
            return  $errorPoint;
        }
    }

    protected function type($history)
    {
        // https://github.com/researchpanelasia/rpa-dominance/wiki/Fulcrum
        // 据说只有 cost(11)
        return CategoryType::FULCRUM_COST;
    }

    protected function task($history)
    {
        return TaskType::SURVEY;
    }

    protected function answerStatus($history){
        $status = strtolower($history['answer_status']);
        return $status;
    }

    protected function approvalStatus($history){
        return strtolower($history['approval_status']);
    }

    protected function comment($history)
    {
        // title is saved in extra_info.title for old history
        $title = isset($history['title']) ? $history['title'] : $history['extra_info']['title'];
        return 'f' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        $sopConfig = $this->getContainer()->getParameter('sop');
        return $sopConfig['api_v1_1_fulcrum_surveys_research_participation_history'];
    }

    protected function requiredFields()
    {
        return array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id',
                     'title','loi','cpi', 'answer_status', 'extra_info');
    }

    protected function skipReward($history)
    {
        if(self::POINT_TYPE_COST != $history['extra_info']['point_type']){
            return true;
        }
        return false;
    }

    protected function skipRewardAlreadyExisted($history)
    {
        try {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $user = $this->getContainer()->get('app.user_service')->getUserBySopRespondentAppMid($history['app_mid']);
            $participations = $em->getRepository('WenwenFrontendBundle:SurveyFulcrumParticipationHistory')->findBy(array(
                'surveyId' => $history['survey_id'],
                'userId' => $user->getId(),
            ));
            if (count($participations) < 3 || count($participations) > 4) {
                $msg = ' Only 3 (targeted, init and forward) or 4 (targeted, init, forward and one of C/S/Q/E) is valid. But now is %d (app_mid = %s).';
                $this->logger->error(__METHOD__ . sprintf($msg, count($participations), $history['app_mid']));
                return true;
            }
            if (count($participations) == 4) {
                foreach ($participations as $participation) {
                    if (in_array($participation->getStatus(), SurveyStatus::$answerStatuses)) {
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' ' . $e->getMessage());
            return true;
        }
        return false;
    }

    protected function createParticipationHistory($history)
    {
        return $this->getContainer()->get('app.survey_fulcrum_service')->createParticipationByAppMid(
            $history['app_mid'],
            $history['survey_id'],
            $this->answerStatus($history)
        );
    }

    protected function getPanelType() {
        return 'Fulcrum';
    }

    protected function preHandle(array $history_list) {
    }
}
