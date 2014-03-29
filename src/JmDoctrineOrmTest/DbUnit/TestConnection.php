<?php
/**
 * Test Connection
 *
 * PHP version 5.4
 *
 * Copyright 2012 Malte Gerth
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */

namespace JmDoctrineOrmTest\DbUnit;

use Doctrine\DBAL\Connection;
use JmDoctrineOrmTest\DbUnit\DoctrineMetadata;
use JmDoctrineOrmTest\DbUnit\DataSet\QueryDataSet;
use JmDoctrineOrmTest\DbUnit\DataSet\QueryTable;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection as IDatabaseConnection;

/**
 * Test Connection
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
class TestConnection implements IDatabaseConnection
{
    /**
     * Database Connection
     *
     * @var Connection
     */
    private $connection = null;

    /**
     * MetaData
     *
     * @var type
     */
    private $metadata = null;

    /**
     * Constructor
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->connection = $conn;
    }

    /**
     * Close this connection.
     *
     * @return void
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * Creates a dataset containing the specified table names. If no table
     * names are specified then it will created a dataset over the entire
     * database.
     *
     * @param array $tableNames Array with table names
     *
     * @return QueryDataSet
     */
    public function createDataSet(Array $tableNames = null)
    {
        $dataSet = new QueryDataSet($this);
        if (!is_array($tableNames)) {
            $tableNames = $this->getMetaData()->getTableNames();
        }

        foreach ($tableNames as $tableName) {
            $dataSet->addTable($tableName);
        }

        return $dataSet;
    }

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $tableName Name of the table
     * @param string $sql       Custom SQL
     *
     * @return QueryTable
     */
    public function createQueryTable($tableName, $sql)
    {
        return new QueryTable($tableName, $sql, $this);
    }

    /**
     * Returns a Doctrine\DBAL\Connection Connection
     *
     * @return Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return DoctrineMetadata
     */
    public function getMetaData()
    {
        if (null === $this->metadata) {
            $this->metadata = new DoctrineMetadata(
                $this->connection->getSchemaManager(),
                $this->connection->getDatabase()
            );
        }

        return $this->metadata;
    }

    /**
     * Returns the number of rows in the given table. You can specify an
     * optional where clause to return a subset of the table.
     *
     * @param string $tableName   Table name
     * @param string $whereClause Custom SQL WHERE clause
     *
     * @return int
     */
    public function getRowCount($tableName, $whereClause = null)
    {
        $sql = 'SELECT count(*) FROM ' . $tableName;
        if ($whereClause !== null) {
            $sql .= ' WHERE ' . $whereClause;
        }

        return $this->connection->fetchColumn($sql);
    }

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->connection->getDatabase();
    }

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object Table name, column name, etc
     *
     * @return string
     */
    public function quoteSchemaObject($object)
    {
        return $this->connection->getDatabasePlatform()
            ->quoteIdentifier($object);
    }

    /**
     * Returns the command used to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand()
    {
        return 'TRUNCATE';
    }

    /**
     * Returns true if the connection allows cascading
     *
     * @return bool
     */
    public function allowsCascading()
    {
        return false;
    }

    /**
     * Disables primary keys if connection does not allow setting them otherwise
     *
     * @param string $tableName Table name
     *
     * @return void
     */
    public function disablePrimaryKeys($tableName)
    {
        $this->getMetaData()->disablePrimaryKeys($tableName);
    }

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName Table name
     *
     * @return void
     */
    public function enablePrimaryKeys($tableName)
    {
        $this->getMetaData()->enablePrimaryKeys($tableName);
    }
}
