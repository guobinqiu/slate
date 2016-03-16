<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\TaskHistory00;
use Wenwen\AppBundle\Entity\SopResearchSurveyParticipationHistory;

class PanelRewardSopPointCommand extends PanelRewardCommand
{
    const USER_AGREEMENT_ACTIVE = 'ACTIVE';
    const TYPE_EXPENSE = AdCategory::ID_QUESTIONNAIRE_EXPENSE;
    const TYPE_TASK = TaskHistory00::TASK_TYPE_SURVEY;

    protected function configure()
    {
        $this->setName('panel:reward-sop-point')
                ->setDescription('request SOP API and reward points based on retrived data')
                ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
                ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start...');

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
        return $this->sop_configure['sop_point_type'][$history['extra_info']['point_type']];
    }

    protected function task($history)
    {
        return self::TYPE_TASK;
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
        $history_model->setType($this->sop_configure['sop_point_type'][$history['extra_info']['point_type']]);
        $em->persist($history_model);
        $em->flush();
    }
}
