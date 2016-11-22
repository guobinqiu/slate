<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\CintResearchSurveyParticipationHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

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

        $this->sop_configure = $this->getContainer()->getParameter('sop');

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
        return $this->sop_configure['api_v1_1_cint_surveys_research_participation_history'];
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
        $records = $em->getRepository('WenwenAppBundle:CintResearchSurveyParticipationHistory')->findBy(array (
            'cintProjectId' => $history['survey_id'],
            'cintProjectQuotaId' => $history['quota_id'],
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
        $history_model = new CintResearchSurveyParticipationHistory();
        $history_model->setCintProjectID($history['survey_id']);
        $history_model->setCintProjectQuotaID($history['quota_id']);
        $history_model->setAppMemberID($history['app_mid']);
        $history_model->setPoint($history['extra_info']['point']);
        $history_model->setType($history['extra_info']['point_type']);
        $em->persist($history_model);
        $em->flush();
    }

    protected function getVendorName() {
        return 'Cint';
    }
}
