<?php

require_once 'creole/ResultSetTest.php';

/**
 * ODBCResultSet tests.
 *
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.2 $
 */
class ODBCResultSetTest extends ResultSetTest {

    /**
     * Test an ASSOC fetch with a connection that does not have the Creole::COMPAT_ASSOC_LOWER flag set.
     *
     * NOTE: This parent method for this test opens a second connection in
     *       order to test the Creole::COMPAT_ASSOC_LOWER flag. I had to override
     *       this because the ODBC driver I was testing with is very sensitive
     *       to having multiple connections open. In particular, I couldn't drop
     *       a database table (via DriverTestManager::initDb()) from the second
     *       connection while the first was still open.
     */

    public function testFetchmodeAssocNoChange() {

        if ($this->conn->getAdapter()->preservesColumnCase())
        {
            $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
            $dsn = DriverTestManager::getDSN();

            $this->conn->close();
            $this->conn->connect($dsn);

            DriverTestManager::initDb($this->conn);

            $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $keys = array_keys($rs->getRow());
            $this->assertEquals("ProductID", $keys[0], 0, "Expected to find mixed-case column name.");
            $rs->close();

            $this->conn->close();
            $this->conn->connect($dsn);
        }
    }

}