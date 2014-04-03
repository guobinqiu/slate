<?Php
namespace Jili\EmarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Psr\Log\LoggerInterface;
use Jili\EmarBundle\Entity\EmarWebsitesCroned;
use Jili\EmarBundle\Api2\Utils\PerRestrict;

class WebsitesCommand extends ContainerAwareCommand 
{
    protected function configure()
    {
        $this
            ->setName('emar:websites')
            ->setDescription('manager emar websites with table advertiserment')
            ->addArgument(
                'wid',
                InputArgument::OPTIONAL,
                'the webiste id'
            )
            ->addOption(
               'start',
               null,
               InputOption::VALUE_OPTIONAL,
               'start from'
            )
            ->addOption(
               'update',
               null,
               InputOption::VALUE_NONE,
               'list emar websits in table advertiserment'
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
            $webListGetter = $this->getContainer()->get('website.list_get');
            $webListGetter->setFields('web_id');
            $webs = $webListGetter->fetch();
            $webDetailGetter = $this->getContainer()->get('website.detail_get');

            $webDetailGetter->setApp('search');

            $pr = new PerRestrict( 500 );

            $start = (int) $input->getOption('start'); // 断点
            $i = 0;
            foreach($webs as $web) {
                $wid= $web['web_id'];
                $i++;

                if( $i < $start) {
                    $logger->error('{jarod}'. implode(':', array(__LINE__,__CLASS__,'ignored','') ). 'i:'.$i . ' wid:'.$wid);
                    continue;
                }

                $pr->add();
                try {
                    $web_detail  = $webDetailGetter->fetch(array('webid'=> $wid));
                    $this->getContainer()->get('website.storage')->save($web_detail );
                } catch( \Exception $e) {

                    $logger->error('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). 'i:'.$i . ' wid:'.$wid);
                    $logger->error('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). 'i:'.$i . ' wid:'.$wid);
                    die();
                }
            }

           $output->writeln( __LINE__ ); 


        } else {


        }

        $output->writeln('ok');
    }
}
