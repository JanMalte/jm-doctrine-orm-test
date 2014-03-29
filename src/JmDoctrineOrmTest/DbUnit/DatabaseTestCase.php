<?php
/**
 * Database Test Case
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

use JmDoctrineOrmTest\DbUnit\TestConnection;
use JmDoctrineOrmTest\DbUnit\Operation\Truncate;
use PHPUnit_Extensions_Database_DataSet_ITable as QueryTable;
use PHPUnit_Extensions_Database_DataSet_IDataSet as QueryDataSet;
use PHPUnit_Extensions_Database_Operation_Composite as CompositeOperation;
use PHPUnit_Extensions_Database_Operation_Factory as OperationFactory;
use PHPUnit_Extensions_Database_TestCase;

/**
 * Database Test Case
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
abstract class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{

    /**
     * Database Test Connection
     *
     * @var TestConnection
     */
    private static $connection = null;

    /**
     * Get the Doctrine Database Connection
     *
     * @return Doctrine\DBAL\Connection
     */
    abstract protected function getDoctrineConnection();

    /**
     * Get the Database Test Connection
     *
     * @return TestConnection
     */
    final protected function getConnection()
    {
        if (self::$connection == null) {
            self::$connection = new TestConnection(
                $this->getDoctrineConnection()
            );
        }

        return self::$connection;
    }

    /**
     * Overrides the parent method to add a custom MySQL truncate operation.
     * This suspends foreign key checks for the duration of the truncate command.
     *
     * @return CompositeOperation
     */
    public function getSetUpOperation()
    {
        return new CompositeOperation(
            array(new Truncate(),OperationFactory::INSERT())
        );
    }

    /**
     * Overrides the parent method to add a custom MySQL truncate operation.
     * This suspends foreign key checks for the duration of the truncate command.
     *
     * @return CompositeOperation
     */
    public function getTearDownOperation()
    {
        return new CompositeOperation(array(new Truncate()));
    }

    /**
     * Create Query DataSet
     *
     * @param array $tableNames Table names
     *
     * @return QueryDataSet
     */
    protected function createQueryDataSet(array $tableNames = null)
    {
        return $this->getConnection()->createDataSet($tableNames);
    }

    /**
     * Create Query DataTable
     *
     * @param string $tableName Table name to create query for
     * @param string $sql       Custom SQL query to use
     *
     * @return QueryTable
     */
    protected function createQueryDataTable($tableName, $sql = null)
    {
        if ($sql == null) {
            $sql = 'SELECT * FROM ' . $tableName;
        }

        return $this->getConnection()->createQueryTable($tableName, $sql);
    }
}
