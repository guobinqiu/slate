<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\FulcrumResearchSurveyParticipationHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

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
        // https://github.com/researchpanelasia/rpa-dominance/wiki/Fulcrum
        // 据说只有 cost(11) 
        if(self::POINT_TYPE_COST == $history['extra_info']['point_type']){
            return CategoryType::FULCRUM_COST;
        } else {
            return 999;
        }
    }

    protected function task($history)
    {
        if(self::POINT_TYPE_COST == $history['extra_info']['point_type']){
            return TaskType::SURVEY;
        } else {
            return 999;
        }
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

    protected function skipRewardAlreadyExisted($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $records = $em->getRepository('WenwenAppBundle:FulcrumResearchSurveyParticipationHistory')->findBy(array (
            'fulcrumProjectId' => $history['survey_id'],
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
