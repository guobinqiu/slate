<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\TaskHistory00;
use Wenwen\AppBundle\Entity\FulcrumResearchSurveyParticipationHistory;

class PanelRewardFulcrumPointCommand extends PanelRewardCommand
{

    const TYPE_TASK = TaskHistory00::TASK_TYPE_SURVEY;

    protected function configure()
    {
      $this->setName('panel:reward-fulcrum-point')
        ->setDescription('request SOP API and reward points based on retrived data')
        ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
        ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start...');
        $this->sop_configure = $this->getContainer()->getParameter('sop');
        $this->setLogger('reward-fulcrum-point');
        return parent::execute($input, $output);

    }

    protected function point($history)
    {
         return  $history['extra_info']['point'];
    }

    protected function type($history)
    {
        $point_exec_type = $history['extra_info']['point_type'];
        return $this->sop_configure['sop_point_type'][$point_exec_type];
    }

    protected function task($history)
    {
        return self::TYPE_TASK;
    }

    protected function comment($history)
    {
        // title is saved in extra_info.title for old history
        $title = isset($history['title']) ? $history['title'] : $history['extra_info']['title'];
        return 'f' . $history['survey_id'] . ' ' . $title;
    }

    protected function url()
    {
        return $this->sop_configure['api_v1_1_fulcrum_surveys_research_participation_history'];

    }

    protected function requiredFields()
    {
        return array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id',
                     'title','loi','cpi', 'answer_status', 'extra_info');
    }

    protected function skipReward($history)
    {
        if ($history['answer_status'] === 'COMPLETE') {
            return false;
        }
        return true;
    }


    protected function createParticipationHistory($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $history_model = new FulcrumResearchSurveyParticipationHistory();

        $history_model->setFulcrumProjectID($history['survey_id']);
        $history_model->setFulcrumProjectQuotaID($history['quota_id']);
        $history_model->setAppMemberID($history['app_mid']);
        $history_model->setPoint($history['extra_info']['point']);
        $history_model->setType($this->type($history));
        $em->persist($history_model);
        $em->flush();
    }


}


