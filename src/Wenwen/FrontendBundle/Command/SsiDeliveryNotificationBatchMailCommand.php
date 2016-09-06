<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class SsiDeliveryNotificationBatchMailCommand extends AbstractBatchMailCommand {

    protected function configure()
    {
        $this->setName('mail:ssi_delivery_notification_batch');
        $this->setDescription('批量通知会员有新的SSI问卷');
        $this->addOption('respondents_id_file', null, InputOption::VALUE_REQUIRED);
    }

    protected function getEmailParams(InputInterface $input){
        $templating = $this->getContainer()->get('templating');
        
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $ssiProjectSurveyParams = $this->parameterService->getParameter('ssi_project_survey');
        $waitDir = $ssiProjectSurveyParams['notification']['wait_dir'];
        $completeDir = $ssiProjectSurveyParams['notification']['complete_dir'];

        $fileName = $input->getOption('respondents_id_file');
        $respondentsIdFile = $waitDir . $fileName;
        $backupFile = $completeDir . $fileName;

        $ssiRespondentIds = $this->getSsiRespondentIds($respondentsIdFile);

        $emailParams = array();

        foreach($ssiRespondentIds as $ssiRespondentId){
            $recipient = $em->getRepository('WenwenAppBundle:SsiRespondent')->retrieveRecipientDataToSendMailById($ssiRespondentId);
            $userEdmUnsubscribes = $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByEmail($recipient['email']);
            if ($recipient && (count($userEdmUnsubscribes) == 0)) {
                $email = $recipient['email'];
                $name1 = $recipient['name1'];
                if ($name1 == null) {
                    $name1 = $email;
                }
                $subject = '亲爱的'.$name1.'，您的新问卷来了！';
                $surveyTitle = 'SSI海外调查';
                $surveyPoint = $ssiProjectSurveyParams['point'];
                $html = $templating->render($this->getTemplatePath(), $this->getTemplateVars($name1, $surveyTitle, $surveyPoint));
                $emailParams[] = array(
                        'email' => $email,
                        'subject' => $subject,
                        'content' => $html
                    );
            }
        }

        $fs = new Filesystem();
        $fs->copy($respondentsIdFile, $backupFile, true);
        $fs->remove($respondentsIdFile);

        return $emailParams;
    }

    public function moveSsiRespondentIdsFile($filePath){

    }

    /**
     * @return array
     */
    private function getSsiRespondentIds($filePath){
        // 一行一个respondentId
        $ssiRespondentIds = array();
        $fh = fopen($filePath, 'r');
        if ($fh) {
            while (($line = fgets($fh)) !== false) {
                $ssiRespondentIds[] = $line;
            }

            fclose($fh);
        } else {
            // error opening the file.
        } 
        return $ssiRespondentIds;
    }

    /**
     * @return string
     */
    private function getTemplatePath()
    {
        return 'WenwenFrontendBundle:EmailTemplate:ssi_delivery_notification.html.twig';
    }

    /**
     * @return array
     */
    private function getTemplateVars($name1, $surveyTitle, $surveyPoint)
    {
        return array(
            'name1' => $name1,
            'survey_title' => $surveyTitle,
            'survey_point' => $surveyPoint,
        );
    }
}