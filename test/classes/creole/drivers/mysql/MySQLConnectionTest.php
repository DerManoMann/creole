<?php

require_once 'creole/ConnectionTest.php';

/**
 * MySQL unit tests.
 *
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.7 $
 */
class MySQLConnectionTest extends ConnectionTest {

    private static $testTransactions = false;

    public function setUp() {
        parent::setUp();

        // check the table types
        $sql = "SHOW TABLE STATUS";
        $rs = $this->conn->executeQuery($sql);
        while($rs->next()) {
            $row = $rs->getRow();
            if ($row['name'] == 'products') {
                if (isset($row['type']) &&
                   ($row['type'] == 'InnoDB' || $row['Type'] == 'BDB')) {
                    self::$testTransactions = true;
                }
                break; // we don't care about the other tables.
            }
        }
        $rs->close();
    }

    public function testSetAutoCommit() {
        if (self::$testTransactions) {
            parent::testSetAutoCommit();
        }
    }

    public function testCommit() {
        if (self::$testTransactions) {
            parent::testCommit();
        }
    }

    public function testRollback() {
        if (self::$testTransactions) {
            parent::testRollback();
        }
    }

    /**
     * Test the applyLimit() method.  By default this method will not modify the values provided.
     * Subclasses must override this method to test for appropriate SQL modifications.
     */
    public function testApplyLimit() {

      /*
        if ( $limit > 0 ) {
            $sql .= " LIMIT " . ($offset > 0 ? $offset . ", " : "") . $limit;
        } else if ( $offset > 0 ) {
            $sql .= " LIMIT " . $offset . ", 18446744073709551615";
        }
        */

        // offset AND limit
        $sql = "SELECT * FROM sampletable WHERE category = 5";

        $sql1 = $sql;
        $this->conn->applyLimit($sql1, 5, 50);
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 5, 50", $sql1);

        // limit only
        $sql2 = $sql;
        $this->conn->applyLimit($sql2, 0, 50);
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 50", $sql2);

        // offset only
        $sql3 = $sql;
        $this->conn->applyLimit($sql3, 5, 0);
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 5, 18446744073709551615", $sql3);

    }

}