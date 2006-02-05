<?php

require_once 'creole/IdGeneratorTest.php';

/**
 * Tests for the ODBC IdGenerator class.
 *
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.1 $
 */
class ODBCIdGeneratorTest extends IdGeneratorTest {

    public function setUp() {
        parent::setUp();
        if (method_exists($this->idgen, 'drop'))
            $this->idgen->drop('idgentest_seq', true);
    }

    /** Ensures that drivers are implementing the correct Id Method. */
    public function testGetMethod() {
        $this->assertEquals(IdGenerator::SEQUENCE, $this->idgen->getIdMethod(), 0, "ODBC Id method should be SEQUENCE (but is not)");
    }

}