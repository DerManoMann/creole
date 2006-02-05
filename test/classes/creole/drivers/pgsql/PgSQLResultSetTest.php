<?php

require_once 'creole/ResultSetTest.php';

/**
 * PgSQLResultSet tests.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.4 $
 */
class PgSQLResultSetTest extends ResultSetTest {            

    /**
     * Test an ASSOC fetch with a connection that does not have the Creole::COMPAT_ASSOC_LOWER flag set.
     */
    public function testFetchmodeAssocNoChange() {
    	
		$this->conn->executeUpdate('CREATE TABLE "CaseTest" ( "ColumnA" INTEGER, "ColumnB" VARCHAR(30) );');
		$this->conn->executeUpdate('INSERT INTO "CaseTest" ( "ColumnA", "ColumnB" ) VALUES (1, \'Hello\');');
		
		$sql = 'SELECT * FROM "CaseTest"';
				
		$rs = $this->conn->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $keys = array_keys($rs->getRow());
        $this->assertEquals("columna", $keys[0], 0, "Expected to find lowercase column name for Postgres.");
        $rs->close();
		
		// no lowercasing
        $conn2 = Creole::getConnection(DriverTestManager::getDSN());        
        $rs = $conn2->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $keys = array_keys($rs->getRow());
        $this->assertEquals("ColumnA", $keys[0], 0, "Expected to find mixed-case column name for Postgres.");
        $rs->close();		
		
		$this->conn->executeUpdate('DROP TABLE "CaseTest";');
		
    }
}