<?php
namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InvitationCommand extends ContainerAwareCommand {
    protected function configure() {
        $this->setName('jili:invitation')->setDescription('Send the invitation letters to certain users.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('start...');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $start = date("Y-m-d", strtotime(' -5 day')) . " 00:00:00";
        $end = date("Y-m-d", strtotime(' -1 day')) . " 23:59:59";
        $users = $em->getRepository('JiliApiBundle:User')->findWenWenUsersForRemmindRegister($start, $end);
        $str = 'jiliactiveregister';
        foreach ($users as $user) {
            $setPasswordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findByUserId($user['id']);
            $code = $setPasswordCode[0]->getCode();

            $wenwen_api_url = $this->getContainer()->getParameter('91wenwen_api_url');
            $url = $wenwen_api_url . '/user/setPassFromWenwen/' . $code . '/' . $user['id'];

            //通过soap发送
            $soapMailLister = $this->getContainer()->get('soap.mail.listener');
            $soapMailLister->setCampaignId($this->getContainer()->getParameter('register_from_wenwen_campaign_id')); //活动id
            $soapMailLister->setMailingId($this->getContainer()->getParameter('register_from_wenwen_mailing_id_again')); //邮件id
            $soapMailLister->setGroup(array (
                'name' => '同时注册后没有激活',
                'is_test' => 'false'
            )); //group
            $recipient_arr = array (
                array (
                    'name' => 'email',
                    'value' => $user['email']
                ),
                array (
                    'name' => 'url_reg',
                    'value' => $url
                )
            );
            $send_email = $soapMailLister->sendSingleMailing($recipient_arr);
            if ($send_email == "Email send success") {
                $output->writeln('Email send success');
            } else {
                $output->writeln('Email send fail');
            }
        }

        $output->writeln('successfully');
    }
}