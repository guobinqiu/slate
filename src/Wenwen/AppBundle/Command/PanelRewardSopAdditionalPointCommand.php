<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\SopResearchSurveyAdditionalIncentiveHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

class PanelRewardSopAdditionalPointCommand extends PanelRewardCommand
{
    const POINT_TYPE_COST = 11;
    const POINT_TYPE_EXPENSE = 61;

    protected function configure()
    {
        $this->setName('panel:reward-sop-additional-point')
                ->setDescription('request SOP additional incentive API and reward points based on retrived data')
                ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
                ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start panel:reward-sop-additional-point: '.date('Y-m-d H:i:s'));

        $app_name = 'site91wenwen';
        $this->setLogger($app_name . '-reward-sop-additional-point');

        $this->sop_configure = $this->getContainer()->getParameter('sop');

        return parent::execute($input, $output);
    }

    protected function point($history)
    {
        return (int) $history['incentive_amount'];
    }

    protected function type($history)
    {
        $categoryType = 999; //先来个扯淡的值
        if(self::POINT_TYPE_EXPENSE == $history['extra_info']['point_type']){
            $categoryType = CategoryType::SOP_EXPENSE;
        } elseif(self::POINT_TYPE_COST == $history['extra_info']['point_type']){
            $categoryType = CategoryType::SOP_COST;
        } else {
            // 据SOP的API说，肯定没有 11 和 61之外的值
            // 原有的逻辑没有判断这里，估计要是出现了这两个之外的值就该扔例外了先不画蛇添足了，
            // 以后得加上值的检查
        }
        return $categoryType;
    }

    protected function task($history)
    {
        $taskType = 999; //先来个扯淡的值
        if(self::POINT_TYPE_EXPENSE == $history['extra_info']['point_type']){
            $taskType = TaskType::RENTENTION;
        } elseif(self::POINT_TYPE_COST == $history['extra_info']['point_type']){
            $taskType = TaskType::SURVEY;
        } else {
            // 据SOP的API说，肯定没有 11 和 61之外的值
            // 原有的逻辑没有判断这里，估计要是出现了这两个之外的值就该扔例外了先不画蛇添足了，
            // 以后得加上值的检查
        }
        return $taskType;
    }

    protected function comment($history)
    {
        $title = $history['title'];
        return 'r' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        return $this->sop_configure['api_v1_1_surveys_research_additional_incentive'];
    }

    protected function requiredFields()
    {
        return array (
            'app_id',
            'app_mid',
            'survey_id',
            'quota_id',
            'title',
            'incentive_amount',
            'hash',
            'created_at',
            'extra_info'
        );
    }

    protected function skipReward($history)
    {
        return false;
    }

    protected function skipRewardAlreadyExisted($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $records = $em->getRepository('WenwenAppBundle:SopResearchSurveyAdditionalIncentiveHistory')->findBy(array (
            'hash' => $history['hash']
        ));
        if (count($records) > 0) {
            return true;
        }
        return false;
    }

    protected function createParticipationHistory($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $history_model = new SopResearchSurveyAdditionalIncentiveHistory();
        $history_model->setSurveyId($history['survey_id']);
        $history_model->setQuotaId($history['quota_id']);
        $history_model->setAppMemberID($history['app_mid']);
        $history_model->setPoint($this->point($history));
        $history_model->setType($this->type($history));
        $history_model->setHash($history['hash']);
        $em->persist($history_model);
        $em->flush();
    }

    protected function extraInfoKeys()
    {
        return array (
            'point_type'
        );
    }
}
