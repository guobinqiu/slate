<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\AppBundle\Entity\FulcrumUserAgreementParticipationHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

class PanelRewardFulcrumAgreementCommand extends PanelRewardCommand
{
    const USER_AGREEMENT_ACTIVE = 'ACTIVE';     
    private $comment = '';
    private $point   = 0;

    protected function configure()
    {
      $this->setName('panel:reward-fulcrum-agreement')
        ->setDescription('request SOP API and reward agreement points based on retrived data')
        ->addArgument('date', InputArgument::REQUIRED, 'the day YYYY-mm-dd')
        ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start panel:reward-fulcrum-agreement: '.date('Y-m-d H:i:s'));
        $this->sop_configure = $this->getContainer()->getParameter('sop');
        $this->comment = '同意Fulcrum问卷调查';
        $this->setLogger('reward-fulcrum-agreement');
        $this->point = 1;

        return parent::execute($input, $output);
    }

    protected function point($history)
    {
        return $this->point;
    }

    protected function type($history)
    {
        return CategoryType::FULCRUM_EXPENSE;
    }

    protected function task($history)
    {
        return TaskType::RENTENTION;
    }

    protected function comment($history)
    {
        return $this->comment;
    }

    protected function url()
    {
        return $this->sop_configure['api_v1_1_fulcrum_user_agreement_participation_history'];
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
    }

}
