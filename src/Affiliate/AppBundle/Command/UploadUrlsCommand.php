<?php
namespace Affiliate\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UploadUrlsCommand extends ContainerAwareCommand
{
    protected $logger;

    protected function configure()
    {
        $this->setName('affiliate:urlUpload');
        $this->setDescription('Upload the urls for affiliate');
        $this->addOption('projectId', null, InputOption::VALUE_REQUIRED, "projectId.");
        $this->addOption('urlFile', null, InputOption::VALUE_REQUIRED, "urlFile.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getOption('projectId');
        $urlFile = $input->getOption('urlFile');

        $rows = array();
        if (($handle = fopen($urlFile, "r")) !== FALSE) {
            while(($row = fgetcsv($handle)) !== FALSE) {
                if(sizeof($row) == 2){
                $rows[] = array(
                    'ukey' => $row[0],
                    'url' => $row[1]
                    );
                }
            }
        }
        $adminProjectService = $this->getContainer()->get('app.admin_project_service');

        $rtn = $adminProjectService->importSurveyUrl($projectId, $rows);

        if($rtn['status'] == 'success'){
            $adminProjectService->openProject($projectId, $rtn['count']);
        } else {
            $adminProjectService->closeProject($projectId);
        }

        $output->writeln('end affiliate:urlUpload: '.date('Y-m-d H:i:s'));
    }

}
