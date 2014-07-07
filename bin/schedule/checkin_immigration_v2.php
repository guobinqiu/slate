<?php

class CheckinImmigration {
    private $dbh;
    private $stmts = array();
    private $tables = array('checkin_click_list', 'checkin_user_list');
    public function __construct() {
        $user = 'root';
        $dsn = 'mysql:dbname=jili_0129;host=127.0.0.1';
        $password = 'MyNewPassword';
        try {
            $this->dbh = new \PDO($dsn, $user, $password);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbh->setAttribute(\PDO::ATTR_AUTOCOMMIT, false );
        } catch (\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function  exec() {
        $tables = $this->tables;
        try {
            $this->dbh->beginTransaction();
            foreach( $tables as $table ) {
                $this->sub_routine( $table);
            }
            $this->dbh->commit();
            // check result
            foreach( $this->stmts as $ind =>  $stmt)  {
                if( $stmt['insert']->rowCount() != $stmt['delete']->rowCount() ) {
                    throw new \Exception( ' insert and delete not mached when immigrate table ' . $tables[$ind]  );
                }
            }
        } catch(\Exception $e ) {
            $this->dbh->rollBack();
            echo "Failed: " . $e->getMessage();
        }
    }

    public function setEndDate(\Datetime $end_date = null) {
        if( empty($end_date)) {
            $end_date = new Datetime( );
        }
        $this->end_date_str = $end_date->format('Y-m-d 00:00:00');
        return $this;
    }

    private function getEndDateStr() {
        if( empty($this->end_date_str)) {
            $this->setEndDate();
        }
        return $this->end_date_str;
    }

    private function  sub_routine( $table) {
        $end_date_str = $this->getEndDateStr();

        $stmt1 = $this->dbh->prepare('INSERT IGNORE INTO `'.$table.'_bk` SELECT * FROM `'.$table.'`  WHERE create_time <  :created_at '); 
        $ret1  = $stmt1->execute(array('created_at'=> $end_date_str ) );
        $stmt3 = $this->dbh->prepare('DELETE  FROM `'.$table.'`  WHERE create_time <  :created_at '); 
        $ret3  = $stmt3->execute(array('created_at'=> $end_date_str ) );
        $this->stmts[] = array( 'insert'=> $stmt1, 'delete'=> $stmt3 ) ;
        return 1;
    } 
}

//  DEMO 
/* Connect to an ODBC database using driver invocation */

$a  = new CheckinImmigration();
//$a->exec();
$a->setEndDate(new \Datetime('2014-06-25'))->exec();


