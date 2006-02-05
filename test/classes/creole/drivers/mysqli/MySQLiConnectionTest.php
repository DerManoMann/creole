<?php
/*
 * $Id: MySQLiConnectionTest.php,v 1.3 2004/09/18 08:52:33 sb Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

require_once 'creole/ConnectionTest.php';

/**
 * Tests for MySQLiConnection.
 *
 *
 * @author Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @version $Revision: 1.3 $
 */
class MySQLiConnectionTest extends ConnectionTest {
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
