<?php

require_once 'creole/ResultSetTest.php';

/**
 * OCI8ResultSet tests.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.2 $
 */
class OCI8ResultSetTest extends ResultSetTest {
    /**
     * Test an ASSOC fetch with a connection that does not have the Creole::COMPAT_ASSOC_LOWER flag set.
     */
    public function testFetchmodeAssocNoChange() {
    
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        
        $conn2 = Creole::getConnection(DriverTestManager::getDSN());
        DriverTestManager::initDb($conn2);
        
        $rs = $conn2->executeQuery($exch->getSql(), ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $keys = array_keys($rs->getRow());
        $this->assertEquals("PRODUCTID", $keys[0], 0, "Expected to find uppercase column name for Oracle.");
        $rs->close();                
    }
}