<?php
/**
 * Doctrine Metadata
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

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use JmDoctrineOrmTest\DbUnit\Exception\TestException;
use PHPUnit_Extensions_Database_DB_IMetaData as DbMetaData;

/**
 * Doctrine Metadata
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
class DoctrineMetadata implements DbMetaData
{

    /**
     * Doctrine Schema Manager
     *
     * @var Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private $schemaManager = null;

    /**
     * Database table cache
     *
     * @var array
     */
    private $tables = null;

    /**
     * Doctrine Database Schema
     *
     * @var string
     */
    private $schema = null;

    /**
     * Constructor
     *
     * @param AbstractSchemaManager $schemaManager Doctrine Schema Manager
     * @param string                $schema        Default Schema
     */
    public function __construct(AbstractSchemaManager $schemaManager, $schema)
    {
        $this->schemaManager = $schemaManager;
        $this->schema = $schema;
    }

    /**
     * Load tables from Schema Manager
     *
     * @return void
     */
    private function loadTables()
    {
        if ($this->tables === null) {
            $tables = $this->schemaManager->listTables();
            $this->tables = array();
            foreach ($tables as $table) {
                $this->tables[strtolower($table->getName())] = $table;
            }
        }
    }

    /**
     * Returns an array containing the names of all the tables in the database.
     *
     * @return array
     */
    public function getTableNames()
    {
        $this->loadTables();

        $tableNames = array();
        foreach ((array) $this->tables as $table) {
            $tableNames[] = $table->getName();
        }

        return $tableNames;
    }

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table,
     *
     * @param string $tableName
     *
     * @return array
     */
    public function getTableColumns($tableName)
    {
        $table = $this->getTable($tableName);

        $columnNames = array();
        foreach ($table->getColumns() as $column) {
            $columnNames[] = $column->getName();
        }

        return $columnNames;
    }

    /**
     * Load table metadata
     *
     * @param  string        $tableName Table name
     * @throws TestException If table does not exist
     *
     * @return object
     */
    private function getTable($tableName)
    {
        $this->loadTables();

        $tableName = strtolower($tableName);
        if (isset($this->tables[$tableName])) {
            return $this->tables[$tableName];
        } else {
            throw new TestException(
                "Table '" . $tableName . "' does not exist in database."
            );
        }
    }

    /**
     * Returns an array containing the names of all the primary key columns in
     * the $tableName table.
     *
     * @param string $tableName Table name
     *
     * @return array
     */
    public function getTablePrimaryKeys($tableName)
    {
        /* @var $table Doctrine\DBAL\Schema\Table */
        $table = $this->getTable($tableName);

        $primaryKey = $table->getPrimaryKey();

        return $primaryKey->getColumns();
    }

    /**
     * Returns the name of the default schema.
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object
     *
     * @return string
     */
    public function quoteSchemaObject($object)
    {
        return $this->schemaManager->getDatabasePlatform()->quoteIdentifier($object);
    }

    /**
     * Returns true if the rdbms allows cascading
     *
     * @return bool
     */
    public function allowsCascading()
    {
        return false;
    }

    /**
     * Disables primary keys if the rdbms does not allow setting them otherwise
     *
     * @param string $tableName
     *
     * @return void
     */
    public function disablePrimaryKeys($tableName)
    {
        return;
    }

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     *
     * @return void
     */
    public function enablePrimaryKeys($tableName)
    {
        return;
    }
}
