<?php
include __DIR__.'/vendor/autoload.php';

class Logger extends \Psr\Log\AbstractLogger
{

    private $file_handler;

    function __construct( $filepath)
    {
        $this->file_hander = fopen( $filepath ,'a+');
    }

    public function log($level, $message, array $context = array())
    {
        $con = '';
        if(count($context) > 0 ) {
            $con = '['.implode('][', $context).']';
        }

        if(is_string($message)) {

        } else if(is_array($message)) {
            $message = json_encode($message, true);
        } else {
            $message  = serialize($message);
        }

        fwrite($this->file_hander,  '['.date('Y-m-d H:i:s').']['.$level.']' .$con. $message. PHP_EOL);
    }

    public function __destructor()
    {
        fclose( $this->file_hander);
    }
}

return 1;

echo 'ok',PHP_EOL;
$logger = new Logger('/tmp/issue714');
$logger->info( 'ok' , array('issue714'));
$logger->warning( 'warning' , array('issue714'));
