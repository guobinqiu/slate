<?php

class TestDataUtils
{
    public static function getTableNames($dbh)
    {
        $table_names = array();
        $sth = $dbh->prepare("SHOW FULL TABLES WHERE Table_type != 'VIEW'");
        $sth->execute();
        while ($row = $sth->fetch(PDO::FETCH_NUM)) {
            $table_names[] = $row[0];
        }
        $sth->closeCursor();

        return $table_names;
    }
    public static function truncateTables($dbh, $table_names = null)
    {
        if (is_null($table_names)) {
            $table_names = self::getTableNames($dbh);
        }

        $dbh->query('SET foreign_key_checks = 0');

        foreach ($table_names as $table_name) {
            $dbh->query("TRUNCATE TABLE {$table_name}");
        }

        $dbh->query('SET foreign_key_checks = 1');
    }
    public static function loadFixture($dbh, $fixture)
    {
        foreach ($fixture as $table_name => $rows) {
            foreach ($rows as $row) {
                $columns = join(',', array_keys($row));
                $values = join(',', array_fill(0, count($row), '?'));

                $dbh->prepare(sprintf('INSERT INTO %s(%s) VALUES(%s)', $table_name, $columns, $values))
                    ->execute(array_values($row));
            }
        }
    }

    public static function buildDatabase($dbh, $fixture)
    {
        self::truncateTables($dbh);
        self::loadFixture($dbh, $fixture);
    }
}
