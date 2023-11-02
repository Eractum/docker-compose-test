<?php
require_once __DIR__.'/database_connection.php';

/** Creates a PDO for an SQL database connection. Extend this class to set it's $path database connection config file path
 */
class DatabaseConnectionSQL implements DatabaseConnection {
    protected static $path;
    static function setConnection() {
        if(!$connectionInfo = parse_ini_file(__DIR__ . static::$path)){
            return 'ERROR: Could not find database connection file.';
        }
        $dsn = "mysql:host=" . $connectionInfo['db_host'] . ";dbname=" . $connectionInfo['db_name'] . ";charset=" . $connectionInfo['db_charset'];
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $connectionInfo['db_user'], $connectionInfo['db_password'], $options);
    }
}