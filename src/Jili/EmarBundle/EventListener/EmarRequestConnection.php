<?php

namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class EmarRequestConnection implements EmarRequestConnectionInterface {

  protected $c ;
  protected $logger;

  protected $app;
  protected $config;
  /**
   * @params: $key  在yiqifa注册的应用时提供的 key
   * @params: $secret 在yiqifa注册的应用时提供的 secret 
   */
  public function __construct($config ) {
      $this->config = $config;
  }

  public function getApp() 
  {
      return $this->app;
  }

  public function setApp( $app_name = '' ) 
  {
#      $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')) );
#      $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($this->config, true)  );
      $app_names = array_keys( $this->config);

      if( empty($app_name) || ! in_array( $app_name ,$app_names ) ) {
          $app_name = $app_names[0]  ;
      } 

      $this->app =  array( $app_name => $this->config[$app_name]);
#      $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($app_name, true)  );
      return $this;
  } 

  public function getConn()
  {
#      $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')) );
#      $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($this->app, true)  );
      $app_config = array_values( $this->app) ;

#      $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($app_config, true)  );

      if(  !isset($app_config[0]) || ! isset($app_config[0]['key']) || ! isset($app_config[0]['secret'])) {
          throw new  \Exception('not config emar app key/secret') ;
      } 
      $key = $app_config[0]['key'];
      $secret = $app_config[0]['secret'];

      $c = new \Jili\EmarBundle\Api2\Utils\YiqifaOpen( $key, $secret) ;
      $c->format="json";

      $this->c = $c;
      return $this->c;
  }

  /**
   * @params: $req 是具体的 emar open.Request类.
   */
  public function exe( $req) {
    $result_raw = $this->getConn()->execute($req);
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