<?php
class TestConnectionUtils
{
    private static $dbConfig = [
      'vagrant' => [
        'dsn' => 'mysql:host=localhost;dbname=jili_db_test',
        'user' => 'root',
        'password' => null,
      ],
      'circle' => [
        'dsn' => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=circle_test',
        'user' => 'ubuntu',
        'password' => null,
      ],
    ];

    public static function getConfig()
    {
        $env = getenv('WW_DB_ENV');
        if (!$env) {
            $env = 'vagrant';
        }
        return self::$dbConfig[$env];
    }

    public static function getConnection()
    {
        $config = self::getConfig();
        return new \PDO($config['dsn'], $config['user'], $config['password']);
    }
}
