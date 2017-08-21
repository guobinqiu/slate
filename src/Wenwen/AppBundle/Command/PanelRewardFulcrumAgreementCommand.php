<?php

namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\FulcrumUserAgreementParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

class PanelRewardFulcrumAgreementCommand extends PanelRewardCommand
{
    const USER_AGREEMENT_ACTIVE = 'ACTIVE';

    protected function configure()
    {
      $this->setName('panel:reward-fulcrum-agreement')
        ->setDescription('request SOP API and reward agreement points based on retrived data')
        ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
        ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db')
        ->addOption('resultNotification', null, InputOption::VALUE_NONE, 'If set, the task will send a notification to system team');
    }

    protected function point($history)
    {
        return 10;
    }

    protected function type($history)
    {
        return CategoryType::FULCRUM_EXPENSE;
    }

    protected function task($history)
    {
        return TaskType::RENTENTION;
    }

    protected function answerStatus($history){
        // agreement积分，不存在csq
        return 'other';
    }

    protected function approvalStatus($history){
        // agreement积分，不存在csq
        return 'other';
    }

    protected function comment($history)
    {
        return '同意Fulcrum问卷调查';
    }

    protected function url()
    {
        $sop_configure = $this->getContainer()->getParameter('sop');
        return $sop_configure['api_v1_1_fulcrum_user_agreement_participation_history'];
    }

    protected function requiredFields()
    {
        return array('app_id','app_mid','agreement_status','answered_at');
    }

    protected function skipReward($history)
    {
        return false;
    }

    protected function skipRewardAlreadyExisted($history)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $records = $em->getRepository('WenwenAppBundle:FulcrumUserAgreementParticipationHistory')->findBy(array (
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
        $history_model = new FulcrumUserAgreementParticipationHistory();
        $history_model->setAppMemberID($history['app_mid']);
        $history_model->setAgreementStatus(self::USER_AGREEMENT_ACTIVE === $history['agreement_status']);
        $em->persist($history_model);
        $em->flush();
        return $history_model;
    }

    protected function getPanelType() {
        return 'Fulcrum Agreement';
    }

    protected function preHandle(array $history_list) {
    }
}
