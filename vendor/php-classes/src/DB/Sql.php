<?php

namespace saes\DB;

class Sql
{
    public const HOSTNAME = "";
    public const USERNAME = "";
    public const PASSWORD = "";
    public const DBNAME = "";

    private $conn;

    public function __construct()
    {
        $this->conn = new \PDO(
            "mysql:dbname=".Sql::DBNAME.";host".Sql::HOSTNAME,
            Sql::USERNAME,
            Sql::PASSWORD
        );
    }

    private function setParameters($statement, $parameters = array()): void
    {
        foreach ($parameters as $key=> $value) {
            $this->setParamater($statement, $key, $value);
        }
    }

    private function setParamater($statement, $key, $value): void
    {
        $statement->bindParam($key, $value);
    }

    private function query($rawQuery, $parameters = array()): void
    {
        $statement = $this->conn->prepare($rawQuery);
        $this->setParameters($statement, $parameters);
        $statement->execute();
    }

    public function select($rawQuery, $parameters = array()): array
    {
        $statement = $this->conn->prepare($rawQuery);
        $this->setParameters($statement, $parameters);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

}
