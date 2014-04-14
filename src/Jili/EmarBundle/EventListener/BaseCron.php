<?php
namespace Jili\EmarBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\EmarBundle\Entity\EmarProductsCron as EmarProductsCron ;

abstract class BaseCron {

    protected $logger;
    protected $em;

    protected $class_name_cron;
    protected $class_name_croned;

    public function duplicateForQuery() {
        $em = $this->em;
        $logger  = $this->logger;

        $cmd_query = $em->getClassMetadata($this->class_name_croned);
        $cmd_cron = $em->getClassMetadata($this->class_name_cron );

        if(  $em->getRepository($this->class_name_cron )->count() == 0 ) {
            throw new Exception ('The '.$this->class_name_cron.' is still empty, nothing copied to '.$this->class_name_croned.'.');
        } ;

        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();

        try {

            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->query('TRUNCATE TABLE '.$cmd_query->getTableName());

            $connection->query('INSERT INTO '.$cmd_query->getTableName() . ' SELECT * FROM '. $cmd_cron->getTableName()  );
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
        #$this->truncate();
    }

    public function truncate() {
        $em = $this->em;
        $logger  = $this->logger;

        $cmd = $em->getClassMetadata($this->class_name_cron);
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('TRUNCATE TABLE '.$cmd->getTableName());
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }
    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }

}
