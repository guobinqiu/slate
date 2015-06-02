<?php
// query the  duomai_api_return
// calc culate the duomai_order coms. 
// update the duomai_order & task_history
class FixDuomaiOrder {
    private $dbh;
    private $stmts = array();

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
        $conn = $this->dbh;
        $sql='select * from duomai_api_return'; 
        $stmt = $conn->query($sql);
        $result = $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $sum = 0;
        while ($row = $stmt->fetch()) {
            $qs = $this->queryStringParser($row['content']);

            if( !isset($qs['id']) || empty($qs['id']) ) {
                continue;
            }

            $result = $this->updateOrder( array(
                'siter_commission' => $qs['siter_commission'],
                'id' => $qs['id'],
                'site_id' => $qs['site_id'],
            ));
            $sum += $qs['siter_commission'];

            echo 'sum( + ',$qs['siter_commission'] ,' ) := '  , $sum ,PHP_EOL;
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
        $sql = 'select id , user_id  from  duomai_order where  ocd = :id and site_id = :site_id and status = 1  and comm = 0 ';
        $sth = $conn->prepare($sql);

        $sth->execute(array('id'=> $params['id'],'site_id'=> $params['site_id'] ));
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        $user_id = $result['user_id'];

        // transaction
        $conn->beginTransaction();

        try{
            $sql = 'update duomai_order set comm = :comm where id = :id and comm = 0 and status =1 limit 1';
            $sth = $conn->prepare($sql);
            $sth->execute(array('id'=> $result['id'],'comm'=> $params['siter_commission'] ));

            $sql_th = 'update task_history0'.($user_id % 10).' set point = :point where  point = 0
                and category_type=23 and user_id = :user_id and order_id = :order_id limit 1';
            $sth_th = $conn->prepare($sql_th);
            $sth_th->execute(array('order_id'=> $result['id'],
                'point'=> floor(70 * $params['siter_commission'] ),
                'user_id'=> $user_id
            ));


            $conn->commit();
        } catch(\Exception $e) {

            $conn->rollBack();
            echo $e->getMessage(),PHP_EOL;
        }

        return ;

    }
}

$a  = new FixDuomaiOrder();
$a->exec();

