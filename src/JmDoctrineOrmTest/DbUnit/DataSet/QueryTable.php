<?php
/**
 * Provides the functionality to represent a database table.
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

namespace JmDoctrineOrmTest\DbUnit\DataSet;

use PHPUnit_Extensions_Database_DataSet_QueryTable as PhpUnitQueryTable;
use PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData as DefaultTableMetaData;

/**
 * Provides the functionality to represent a database table.
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
class QueryTable extends PhpUnitQueryTable
{

    /**
     * Load the data
     *
     * @return void
     */
    protected function loadData()
    {
        if ($this->data === null) {
            $this->data = $this->databaseConnection->getConnection()
                ->fetchAll($this->query);
        }
    }

    /**
     * Create table meta data
     *
     * @return void
     */
    protected function createTableMetaData()
    {
        if ($this->tableMetaData === null) {
            // Load data
            $this->loadData();

            $columnNames = $this->databaseConnection->getMetaData()
                ->getTableColumns($this->tableName);

            $this->tableMetaData = new DefaultTableMetaData(
                $this->tableName,
                $columnNames
            );
        }
    }
}
