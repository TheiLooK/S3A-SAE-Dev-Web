<?php
declare(strict_types=1);
namespace touiteur\app\db;
use PDO;

class ConnectionFactory{


    /**
     * @var array
     */
    private static array $config =[];

    /**
     * @var PDO|null
     */
    private static ?PDO $db=null;


    /**
     * Function used to recover the file config
     * @param $filename
     *
     */
    static function setconfig($filename) {
        self::$config = parse_ini_file($filename);
    }


    /**
     * Function used to connect to the database
     * @return mixed
     */
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