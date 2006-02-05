<?php

require_once 'creole/IdGeneratorTest.php';

/**
 * Tests for the MySQL IdGenerator class.
 * 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.1 $
 */
class MySQLIdGeneratorTest extends IdGeneratorTest {

    
    /** Ensures that drivers are implementing the correct Id Method. */
    public function testGetMethod() {
        $this->assertEquals(IdGenerator::AUTOINCREMENT, $this->idgen->getIdMethod(), 0, "MySQL Id method should be AUTOINCREMENT (but is not)");
    }
    
}