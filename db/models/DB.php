<?php

$debug = true;

if ($debug)
    require (dirname(__FILE__) . '/..') . "/db_config_dev.php";
else
    require (dirname(__FILE__) . '/..') . "/db_config_prod.php";

abstract class DB
{
    // Protected so child's can access these attributes
    protected $dbPath = DB_DATABASE;
    protected $dbExtension = '.db';
    protected $delimiter = ',';
    /**
     * @var mysqli
     */
    public $connection;

    public function __construct()
    {
        $this->dbPath = DB_DATABASE;
        $this->connection = new mysqli(
            DB_SERVER_URL,
            DB_USERNAME,
            DB_PASSWORD,
            DB_DATABASE
        );
        if ($this->connection->connect_error) {
            exit("Connection to DB failed: " . $this->connection->connect_error);
        }
    }

    public function __toString()
    {
        return "database config: dbPath: $this->dbPath, dbExtenstion: $this->dbExtension, delimiter: $this->delimiter";
    }

    protected function escape($string)
    {
        if ($string == null)
            return null;
        return mysqli_escape_string($this->connection, $string);
    }

    public function commit()
    {
        $this->connection->commit();
    }


    protected function query($query_string)
    {
        $result = $this->connection->query($query_string);

        if ($result && mysqli_num_rows($result) == 1)
            return $result->fetch_assoc();
        else if ($result && mysqli_num_rows($result) > 1)
            return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }

    protected function non_return_query($query_string)
    {
        try {
            return $this->connection->query($query_string);
        } catch (mysqli_sql_exception $ex) {
            return false;
        }
    }

    public function configInfo()
    {
        echo $this;
    }
}