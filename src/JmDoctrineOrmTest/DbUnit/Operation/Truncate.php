<?php
/**
 * Doctrine Truncate Operation
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

namespace JmDoctrineOrmTest\DbUnit\Operation;

use Exception;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection as IDatabaseConnection;
use PHPUnit_Extensions_Database_DataSet_IDataSet as IDataSet;
use PHPUnit_Extensions_Database_Operation_Exception as OperationException;
use PHPUnit_Extensions_Database_Operation_Truncate;

/**
 * Doctrine Truncate Operation
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
class Truncate extends PHPUnit_Extensions_Database_Operation_Truncate
{

    /**
     * Flag if the foreign_key_check is disabled
     *
     * @var boolean
     */
    protected $noForeignKeyCheck = true;

    /**
     * Get if the foreign_key_check is disabled
     *
     * @return boolean
     */
    public function isNoForeignKeyCheck()
    {
        return (boolean) $this->noForeignKeyCheck;
    }

    /**
     * Disable the foreign_key_check
     *
     * @param boolean $noForeignKeyCheck Disable the foreign_key_check
     *
     * @return Truncate Provides a fluent interface
     */
    public function setNoForeignKeyCheck($noForeignKeyCheck)
    {
        $this->noForeignKeyCheck = (boolean) $noForeignKeyCheck;

        return $this;
    }

    /**
     * Executes a truncate against all tables in a dataset.
     *
     * @see PHPUnit_Extensions_Database_Operation_Truncate::execute()
     *
     * @param IDatabaseConnection $connection Database Connection
     * @param IDataSet            $dataSet    Data Set
     *
     * @return void
     */
    public function execute(IDatabaseConnection $connection, IDataSet $dataSet)
    {
        // Disable the foreign_key_check temporarily
        if ($this->isNoForeignKeyCheck()) {
            $connection->getConnection()->exec(
                'SET @PREVIOUS_foreign_key_checks = @@foreign_key_checks'
            );
            $connection->getConnection()->exec(
                'SET @@foreign_key_checks = 0'
            );
        }

        // Execute the Truncate operation
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */
            $tableName = $connection->quoteSchemaObject(
                $table->getTableMetaData()->getTableName()
            );

            $query = $connection->getConnection()->getDatabasePlatform()
                ->getTruncateTableSql(
                    $tableName,
                    $this->useCascade
                );

            try {
                $connection->getConnection()->executeUpdate($query);
            } catch (Exception $e) {
                throw new OperationException(
                    'TRUNCATE',
                    $query,
                    array(),
                    $table,
                    $e->getMessage()
                );
            }
        }

        // Reset the state of foreign_key_check
        if ($this->isNoForeignKeyCheck()) {
            $connection->getConnection()->exec(
                'SET @@foreign_key_checks = @PREVIOUS_foreign_key_checks'
            );
        }
    }
}
