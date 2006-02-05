<?php

require_once 'creole/ConnectionTest.php';

/**
 * SQLiteConnection tests.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.4 $
 */
class SQLiteConnectionTest extends ConnectionTest {
    
    
    /**
     * Test the applyLimit() method.  By default this method will not modify the values provided.
     * Subclasses must override this method to test for appropriate SQL modifications.
     */
    public function testApplyLimit() {        
    
    /*if ( $limit > 0 ) {
            $sql .= " LIMIT ".$limit.( $offset > 0 ? ", " . $offset : "" );
        } elseif ( $offset > 0 ) {
            $sql .= " LIMIT -1, ".$offset;
        }*/
        
        // offset AND limit    
        $sql = "SELECT * FROM sampletable WHERE category = 5";        
        
        $sql1 = $sql;
        $this->conn->applyLimit($sql1, 5, 50);        
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 50 OFFSET 5", $sql1);
        
        // limit only
        $sql2 = $sql;
        $this->conn->applyLimit($sql2, 0, 50);
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 50", $sql2);
        
        // offset only
        $sql3 = $sql;
        $this->conn->applyLimit($sql3, 5, 0);
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT -1 OFFSET 5", $sql3);                
        
    }
}
