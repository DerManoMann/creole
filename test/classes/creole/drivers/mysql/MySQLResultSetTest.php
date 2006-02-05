<?php

require_once 'creole/ResultSetTest.php';

/**
 * MySQLResultSet tests.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.2 $
 */
class MySQLResultSetTest extends ResultSetTest {
        
	/**
     * Unfortunatley MySQL always applies rtrim() on strings ....
     */
    public function testUntrimmedGet() {
        
		$str = "TEST    ";
		
        $exch = DriverTestManager::getExchange('ResultSetTest.setString.RTRIM');
        $stmt = $this->conn->prepareStatement($exch->getSql());
        $stmt->setString(1, $str);
        $stmt->setInt(2, 1);
        $stmt->executeUpdate();
        $stmt->close();
                            
        $exch = DriverTestManager::getExchange('ResultSetTest.getString.RTRIM');
        $stmt = $this->conn->prepareStatement($exch->getSql());
        $stmt->setInt(1, 1);
        $rs = $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
        $rs->next();
        $this->assertEquals(rtrim($str), $rs->getString(1));
        
        $stmt->close();
        $rs->close();
    }
		
}