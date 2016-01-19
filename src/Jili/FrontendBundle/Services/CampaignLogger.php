<?php

namespace Jili\FrontendBundle\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class CampaignLogger 
{

    private $logger ;

    public function __construct( $log_dir )
    {
        $logger = new Logger('campaign_code');
        $log_dir = $log_dir.date('/Y/m/d/');

        $path =  $log_dir .'campaign_code.log';

        $fs = new Filesystem();
        if( ! $fs->exists($log_dir) ) {
            try {
                $fs->mkdir($log_dir );
            } catch (IOExceptionInterface $e) {
                throw "An error occurred while creating your directory at ".$e->getPath();
            }
        }

#        $dateFormat = 'Y n j, g:i a';
        $dateFormat = '';
        // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        $output = "%message%\n";

        // finally, create a formatter
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler($path, Logger::INFO);
        $stream->setFormatter($formatter);

        $logger->pushHandler($stream);
        $this->logger = $logger;
    }

    public function track(array $message) 
    {
        if ( empty($message) || ! isset($message['campaign_code'])|| empty(trim($message['campaign_code'])) ) {
            return;
        }

        $this->logger->addInfo( json_encode($message)   );
    }
   
}

