<?Php
namespace Jili\EmarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Psr\Log\LoggerInterface;

class WebsitesCommand extends ContainerAwareCommand 
{
    protected function configure()
    {
        $this
            ->setName('emar:websites')
            ->setDescription('manager emar websites by table advertiserment')
            ->addArgument(
                'wid',
                InputArgument::OPTIONAL,
                'the webiste id'
            )
            ->addOption(
               'list',
               null,
               InputOption::VALUE_NONE,
               'list emar websits in table advertiserment'
            )
            ->addOption(
               'update',
               null,
               InputOption::VALUE_NONE,
               'update emar websits in table advertiserment'
            )
            ->addOption(
               'remove',
               null,
               InputOption::VALUE_NONE,
               'set the delte_flag to 1 in table advertiserment, wid required'
            );
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $logger LoggerInterface */
        $logger = $this->getContainer()->get('logger');

        $em  = $this->getContainer()->get('doctrine')->getManager( );

        $wid = $input->getArgument('wid');

        if ($wid ) {

        }

        if ($input->getOption('update')) {


        } else if ($input->getOption('remove')) {


        } else {


        }

        $output->writeln('ok');
    }
}
