<?php
/**
 * Provides access to a database instance as a data set.
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

use JmDoctrineOrmTest\DbUnit\DataSet\QueryTable;
use PHPUnit_Extensions_Database_DataSet_QueryDataSet as PhpUnitQueryDataSet;

/**
 * Provides access to a database instance as a data set.
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
class QueryDataSet extends PhpUnitQueryDataSet
{
    /**
     * Add table
     *
     * @param string $tableName
     * @param string $query
     *
     * @return void
     */
    public function addTable($tableName, $query = null)
    {
        if ($query === null) {
            $query = 'SELECT * FROM ' . $tableName;
        }

        $this->tables[$tableName] = new QueryTable(
            $tableName,
            $query,
            $this->databaseConnection
        );
    }
}
