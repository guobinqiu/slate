<?php
include __DIR__.'/Logger.php';

// query the  duomai_api_return
// calc culate the duomai_order coms. 
// update the duomai_order & task_history
class FixDuomaiOrder {
    private $dbh;
    private $logger;

    // 数据库连接
    public function __construct() 
    {
        $dsn = 'mysql:dbname=zili_dev;host=192.168.1.235';
        $user = 'root';
        $password = 'ecnavi';

        try {
            $this->dbh = new \PDO($dsn, $user, $password);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbh->setAttribute(\PDO::ATTR_AUTOCOMMIT, false );
        } catch (\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function exec() 
    {
        //取得duomai历史 请求参数，
        $conn = $this->dbh;
        $sql='select * from duomai_api_return'; 
        $stmt = $conn->query($sql);
        $result = $stmt->setFetchMode( PDO::FETCH_ASSOC );

        // 记录 请求记录中的 siter_commission和（可能会有重复的, 因为相同的订单可能会2次请求)
        //$sum = 0;
        $i=0;
        while ($row = $stmt->fetch()) {
            $qs = $this->queryStringParser($row['content']);

            if( !isset($qs['id']) || empty($qs['id']) ) {
                continue;
            }

            $this->logInfo($i++);

            $result = $this->updateOrder( array(
                'siter_commission' => $qs['siter_commission'],
                'id' => $qs['id'],
                'site_id' => $qs['site_id'],
            ));

        }
    }


    // parse the  query string from callback
    private function queryStringParser($content) 
    {
        $a = parse_url($content);
        if ( !isset($a['query'])) {
            return ;
        }
        parse_str($a['query'], $arr);
        return $arr;
    }


    // array(site_id, id, siter_commission 
    private function updateOrder($params) 
    {
        
        $conn = $this->dbh;
        // 根据请求参数 取得 order 记录
        $sql = 'select id , user_id  from  duomai_order where  ocd = :id and site_id = :site_id and status = 1  and comm = 0 ';
        $sth = $conn->prepare($sql);
        $data = array('id'=> $params['id'],'site_id'=> $params['site_id'] );
        $r = $sth->execute($data);

        $this->logSqlExecuted($sql, $data);
        $this->logInfo('result: '. $r);

        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $user_id = $result['user_id'];

        if( ! $result)  {
            $this->logInfo( 'empty returned');
            return;
        }

        $this->logInfo( $result);
        // transaction
        $conn->beginTransaction();

        try{

        //  修改 order 记录中的comm字段，
            $sql = 'update duomai_order set comm = :comm where id = :id and comm = 0 and status =1 limit 1';
            $sth = $conn->prepare($sql);
            $data = array('id'=> $result['id'],'comm'=> $params['siter_commission'] );
            $r = $sth->execute($data);

            $this->logSqlExecuted($sql, $data);
            $this->logInfo('result: '. $r);


            //  修改 对应的task_history记录中的point字段，
            $sql_th = 'update task_history0'.($user_id % 10).' set point = :point where  point = 0 and category_type=23 and user_id = :user_id and order_id = :order_id limit 1';

            $sth_th = $conn->prepare($sql_th);
            $data_th = array('order_id'=> $result['id'],
                'point'=> floor(70 * $params['siter_commission'] ),
                'user_id'=> $user_id
            );
            $r_th = $sth_th->execute($data_th);
            $this->logSqlExecuted($sql_th, $data_th);
            $this->logInfo('result: '. $r_th);

            $conn->commit();
        } catch(\Exception $e) {

            $conn->rollBack();
            if($this->logger) {
                $this->logger->error($e->getMessage(), array('issue714'));
            }
        }

        return ;

    }

    // the logger setter 
    public function setLogger( \Psr\Log\LoggerInterface $logger )
    {
        $this->logger = $logger;
        return $this;
    }

    // wirte the $sql with  $params 
    private function logSqlExecuted($sql, $params) {
        if( ! $this->logger ) {
            return ;
        }

        $logger = $this->logger;
        $keys = array_keys($params);
        array_walk($keys, function(&$item) { $item = ':'.$item;} );
        $values = array_values($params);
        $sql = str_replace($keys, $values, $sql);

        $sql = str_replace(array("\r","\r\n", "\n", "\n\r"),' ' , $sql);

        $logger->info($sql, array('issue714'));
    } 

    private function logInfo($message) 
    {
        if( ! $this->logger ) {
            return ;
        }

        $logger = $this->logger;
        $logger->info($message, array('issue714'));
    }
}

// config the logger
$logger = new Logger('/tmp/issue714.log');

// set the logger 
$a  = new FixDuomaiOrder();
$a->setLogger($logger)
    ->exec();
