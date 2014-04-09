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
            $webListGetter->setApp('cron');
            $webListGetter->setFields('web_id');
            $webs = $webListGetter->fetch();

            $logger = $this->getContainer()->get('logger');

            $webDetailGetter = $this->getContainer()->get('website.detail_get');
            $webDetailGetter->setApp('cron');

            $pr = new PerRestrict( 500 );

            $start = (int) $input->getOption('start'); // 断点

            $i = 0;
            $webs_failed = array();

            $round = 0;

            $this->getContainer()->get('cron.websites')->truncate();
            do{
                $webs_todo = array_merge($webs_failed, $webs) ;
                foreach($webs_todo  as $web) {
                    $wid= $web['web_id'];
                    $i++;

                    if( $i < $start) {
                        $logger->error('{jarod}'. implode(':', array(__LINE__,__CLASS__,'ignored','') ). 'i:'.$i . ' wid:'.$wid);
                        continue;
                    }

                    $pr->add();
                    // try 3 times when exception happened , with sleep();
                    for($i = 0; $i <= 3 ; $i++ ) {
                        try {
                            $web_detail  = $webDetailGetter->fetch(array('webid'=> $wid));
                            $this->getContainer()->get('cron.websites')->save($web_detail );
                            break;
                        } catch( \Exception $e) {

                            if( $i === 3) {
                                $webs_failed[] = $wid;
                                $logger->error('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). 'i:'.$i . ' wid:'.$wid);
                                $logger->error('{jarod}'. implode(':', array(__LINE__,__CLASS__,'') ). 'i:'.$i . ' wid:'.$wid);
                            } else {
                                $output->writeln( 'Sleeping.. for wid: '. $wid ); 
                                sleep($i * $i);
                                continue;
                            }
                        } 
                    }
                }

            } while( !empty( $webs_failed) && 3 > $round++ );

            if(!empty( $webs_failed) )  {
                // output the un completed webids.
                // email it ? right ??
                $output->writeln('failed web ids: ' .var_export( $webs_failed,true) );
            }

            $this->getContainer()->get('cron.websites')->duplicateForQuery();
            //todo: try the failed webs again
            //todo: while( the web_failed is empty);
            //todo: insert all web_id in advanced, then updated the record by web_id.
            //  leave the error postprone fields ignored in 1st round with websites.list.get api, 
            //  fetch those fields in 2nd round by websites.get api.


        } else {


        }

        $output->writeln('ok');
    }
}
