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
        ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start panel:reward-fulcrum-point: '.date('Y-m-d H:i:s'));
        $this->setLogger('reward-fulcrum-point');
        return parent::execute($input, $output);
    }

    protected function point($history)
    {
         return  $history['extra_info']['point'];
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

    protected function comment($history)
    {
        // title is saved in extra_info.title for old history
        $title = isset($history['title']) ? $history['title'] : $history['extra_info']['title'];
        return 'f' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        $sop_configure = $this->getContainer()->getParameter('sop');
        return $sop_configure['api_v1_1_fulcrum_surveys_research_participation_history'];
    }

    protected function requiredFields()
    {
        return array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id',
                     'title','loi','cpi', 'answer_status', 'extra_info');
    }

    protected function skipReward($history)
    {
        if ($history['answer_status'] != 'COMPLETE') {
            // 20160718
            // 状态不为 COMPLETE的不处理
            // 老实说，这样做很不干净
            // 状态不为 COMPLETE的不发积分，但是记录得要啊
            return true;
        }
        if(self::POINT_TYPE_COST != $history['extra_info']['point_type']){
            return true;
        }
        return false;
    }

    protected function skipRewardAlreadyExisted($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userId = $this->getContainer()->get('app.user_service')->toUserId($history['app_mid']);
        $participations = $em->getRepository('WenwenFrontendBundle:SurveyFulcrumParticipationHistory')->findBy(array(
            //'appMid' => $history['app_mid'],
            'surveyId' => $history['survey_id'],
            'userId' => $userId,
        ));
        //先注释掉，积累一段时间数据后再放开
//        if (count($participations) < 3) {
//            throw new \Exception('菲律宾那边有误操作，没回答过的用户也撒钱，钱多是吗？');
//        }
//        if (count($participations) == 4) {
            foreach ($participations as $participation) {
                if (in_array($participation->getStatus(), SurveyStatus::$answerStatuses)) {
                    return true;
                }
            }
//        }
        return false;
    }

    protected function createParticipationHistory($history)
    {
        return $this->getContainer()->get('app.survey_fulcrum_service')->createParticipationByAppMid(
            $history['app_mid'],
            $history['survey_id'],
            $history['answer_status']
        );
    }

    protected function getPanelType() {
        return 'Fulcrum';
    }
}
