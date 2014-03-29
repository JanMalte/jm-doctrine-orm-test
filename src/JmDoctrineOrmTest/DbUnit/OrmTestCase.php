<?php
/**
 * ORM Test Case
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

use JmDoctrineOrmTest\DbUnit\Event\EntityManagerEventArgs;

/**
 * ORM Test Case
 *
 * @author     Malte Gerth <mail@malte-gerth.de>
 * @copyright  2012 Malte Gerth
 * @license    Apache-2.0
 * @link       http://www.malte-gerth.de/
 * @since      2012-12-27
 * @see        http://jeremycook.ca/2012/02/27/making-phpunit-doctrine-mysql-play-nicely/
 */
abstract class OrmTestCase extends DatabaseTestCase
{

    /**
     * Doctrine Entity Manager
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    /**
     * Method to create the entity manager, used by parent methods.
     *
     * @see OrmTestCase::createEntityManager()
     *
     * @return \Doctrine\ORM\EntityManager
     */
    abstract protected function createEntityManager();

    /**
     * Get the Doctrine Entity Manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    final protected function getEntityManager()
    {
        if ($this->entityManager == null) {
            $this->entityManager = $this->createEntityManager();
            $this->assertInstanceOf(
                'Doctrine\ORM\EntityManager',
                $this->entityManager,
                "Not a valid Doctrine\ORM\EntityManager returned from createEntityManager() method."
            );
        }

        return $this->entityManager;
    }

    /**
     * {@inheritDoc}
     */
    final protected function getDoctrineConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->databaseTester = null;

        $eventManager = $this->getEntityManager()->getEventManager();
        if ($eventManager->hasListeners('preTestSetUp')) {
            $eventManager->dispatchEvent(
                'preTestSetUp',
                new EntityManagerEventArgs($this->getEntityManager())
            );
        }

        $tester = $this->getDatabaseTester();

        $tester->setSetUpOperation($this->getSetUpOperation());
        $tester->setDataSet($this->getDataSet());
        $tester->onSetUp();

        if ($eventManager->hasListeners('postTestSetUp')) {
            $eventManager->dispatchEvent(
                'postTestSetUp',
                new EntityManagerEventArgs($this->getEntityManager())
            );
        }
    }

    /**
     * Creates a IDatabaseTester for this testCase.
     *
     * @return PHPUnit_Extensions_Database_ITester
     */
    protected function newDatabaseTester()
    {
        return new DatabaseTester($this->getConnection());
    }
}
