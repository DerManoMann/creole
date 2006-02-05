<?php

require_once 'creole/ConnectionTest.php';

/**
 * OCI8Connection tests.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.1 $
 */
class OCI8ConnectionTest extends ConnectionTest {
    
    /**
     * Test the applyLimit() method for appropriate behavior w/ SQLite.
     */
    public function testApplyLimit() {
        
        throw new SQLException("needs to be implemented for Oracle");
        
        $sql = "SELECT * FROM sampletable WHERE category = 5";        
        $offset = 5;
        $limit = 50;
        
        $this->conn->applyLimit($sql, $offset, $limit);
        
        // make sure nothing changed       
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 50 OFFSET 5", $sql);
    }
}