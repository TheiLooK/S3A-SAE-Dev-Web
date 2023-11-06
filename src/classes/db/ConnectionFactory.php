<?php
declare(strict_types=1);
namespace touiteur\app\db;
class ConnectionFactory{
    private static array $config =[];
    private static ?PDO $db=null;

    static function setconfig($filename) {
        self::$config = parse_ini_file($filename);
    }

    static function makeConnection(): mixed{
        if (self::$db == null) {
            $dsn = self::$config['driver'].
                ':host='.self::$config['host'].
                ';dbname='.self::$config['database'];

            try {
                self::$db = new PDO($dsn, self::$config['username'], self::$config['password'], [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]);
            } catch (PDOException $e) {
                die('Erreur : '.$e->getMessage());
            }

            self::$db->prepare("SET NAMES 'utf8'")->execute();
        }
        return self::$db;
    }

}