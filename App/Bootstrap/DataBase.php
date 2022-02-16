<?php

declare(strict_types=1);

namespace app\Bootstrap;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Class DataBase
 *
 * Responsible for performing any operation within the database
 *
 * @package app\Bootstrap
 */
class DataBase
{
    public PDO $DataBase;
    private array $Settings;

    /**
     * Create the object
     */
    public function CreateDataBase()
    {
        $this->Settings = [
            'host' => DB_SERVE,
            'port' => DB_PORT,
            'user' => DB_USER,
            'password' => DB_PASS,
            'dbname' => DB_NAME,
            'charset' => DB_CHARSET,
        ];

        $this->Connect();
    }

    /**
     * Create PDO instance
     */
    protected function Connect()
    {
        $dsn = 'mysql:dbname=' . $this->Settings["dbname"] . ';host=';
        $dsn .= $this->Settings["host"] . ';port=' . $this->Settings['port'];

        $MySQL_Attr = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . (! empty($this->settings['charset']) ? $this->settings['charset'] : 'utf8'),
        ];

        $this->DataBase = new PDO($dsn, $this->Settings["user"], $this->Settings["password"], $MySQL_Attr);

        $this->DataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->DataBase->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        $this->DataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * Close the connection
     */
    public function CloseConnection()
    {
        unset($this->DataBase);
    }

    /**
     * Execution
     *
     * @param string $Query
     * @param array $Parameters
     *
     * @throws PDOException
     *
     * @return mixed
     */
    protected function execute(string $Query, array $Parameters = []): mixed
    {
        try {
            if (! isset($this->DataBase) || is_null($this->DataBase)) {
                $this->connect();
            }

            $this->DataBase->beginTransaction();

            $Statement = @$this->DataBase->prepare($Query);

            $Statement = $this->Bind($Statement, $Parameters);

            $Response = $Statement->execute();

            $this->DataBase->commit();
        } catch (PDOException $Exception) {

            // Reconnect once when the server is disconnected
            if ((string)$Exception->errorInfo[1] === "2006" || (string)$Exception->errorInfo[2] === "2013") {
                $this->closeConnection();

                $this->connect();

                $this->execute($Query, $Parameters);
            }

            $this->DataBase->rollBack();

            throw new PDOException("SQL: $Query " . $Exception->getMessage(), (int)$Exception->getCode());
        }

        return $Response;
    }

    /**
     *
     * Bind values
     *
     * @param PDOStatement $Statement
     * @param array $properties
     * @return PDOStatement
     */
    private function Bind(PDOStatement $Statement, array $properties): PDOStatement
    {
        if (! empty($properties)) {
            foreach ($properties as $key => $value) {
                $Statement->bindParam($key, $value);
            }
        }

        return $Statement;
    }
}
