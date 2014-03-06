<?php

namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class EmarRequestConnection{

  protected $c ;
  protected $logger;

  /**
   * @params: $key  在yiqifa注册的应用时提供的 key
   * @params: $secret 在yiqifa注册的应用时提供的 secret 
   */
  public function __construct($key, $secret) {
    $c = new \Jili\EmarBundle\Api2\Utils\YiqifaOpen( $key, $secret) ;
    $c->format="json";
    $this->c = $c;
  }


  /**
   * @params: $req 是具体的 emar open.Request类.
   */
  public function exe( $req) {
    $result_raw = $this->c->execute($req);
    // 对返回的json 转义为有效的json string.
    $result_escaped = trim(str_replace(array( "\\","{\n", "}\n", ",\n", "]\n", "\"\n", "\n","\r",) , array('\\\\', '{', '}', ',', ']','"', '\n','') ,trim($result_raw)));

    $result  = json_decode( trim($result_escaped), true);  

    if( isset($result['errors'] )  
      && isset($result['errors']['error'] ) 
      && isset($result['errors']['error'][0] ) 
      && isset($result['errors']['error'][0]['msg'] )  ) {

        $error_msg = trim($result['errors']['error'][0]['msg'] );

        if( 'results is empty' == $error_msg ) {

          $return = array();

        } else {
          $this->logger->crit(implode(':', array( __CLASS__, __LINE__,'')) . $error_msg);
          throw new \Exception($error_msg);

        }
      } else if( is_null($result) ) {

        $error_msg = 'JSON parsed error' ;

        $this->logger->crit(implode(':', array( __CLASS__, __LINE__,'message','')) .$error_msg );
        $this->logger->crit(implode(':', array( __CLASS__, __LINE__,'result_raw','')) . $result_raw );
        $this->logger->crit(implode(':', array( __CLASS__, __LINE__,'result_escaped','')) . $result_escaped );

        throw new \Exception($error_msg);

      } else {

        $return= $result['response'];
      }

    return $return ;  
  }

  public function setLogger(  LoggerInterface $logger) {
    $this->logger = $logger;
  }
}
