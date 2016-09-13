<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\SopResearchSurveyParticipationHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start panel:reward-sop-point: '.date('Y-m-d H:i:s'));

        $app_name = 'site91wenwen';
        $this->setLogger($app_name . '-reward-sop-point');

        $this->sop_configure = $this->getContainer()->getParameter('sop');

        return parent::execute($input, $output);
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

    protected function comment($history)
    {
        // title is saved in extra_info.title for old history
        $title = isset($history['title']) ? $history['title'] : $history['extra_info']['title'];
        return 'r' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        return $this->sop_configure['api_v1_1_surveys_research_participation_history'];
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
        $em = $this->getContainer()->get('doctrine')->getManager();
        $records = $em->getRepository('WenwenAppBundle:SopResearchSurveyParticipationHistory')->findBy(array (
            'partnerAppProjectId' => $history['survey_id'],
            'appMemberId' => $history['app_mid']
        ));
        if (count($records) > 0) {
            return true;
        }
        return false;
    }

    protected function createParticipationHistory($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $history_model = new SopResearchSurveyParticipationHistory();
        $history_model->setPartnerAppProjectID($history['survey_id']);
        $history_model->setPartnerAppProjectQuotaID($history['quota_id']);
        $history_model->setAppMemberID($history['app_mid']);
        $history_model->setPoint($history['extra_info']['point']);
        $history_model->setType([$history['extra_info']['point_type']);
        $em->persist($history_model);
        $em->flush();
    }
}
