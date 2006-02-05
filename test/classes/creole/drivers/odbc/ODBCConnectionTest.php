<?php

require_once 'creole/ConnectionTest.php';

/**
 * ODBCConnection tests.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.1 $
 */
class ODBCConnectionTest extends ConnectionTest {

    /**
     * Test the applyLimit() method.  By default this method will not modify the values provided.
     * Subclasses must override this method to test for appropriate SQL modifications.
     */
    public function testApplyLimit() {

        $sql = "SELECT * FROM sampletable WHERE category = 5";
        $offset = 5;
        $limit = 50;

        if (!$this->conn->getAdapter()->hasLimitOffset())
        {
            $this->conn->applyLimit($sql, $offset, $limit);
            $this->assertEquals("SELECT * FROM sampletable WHERE category = 5", $sql);
        }
    }

}