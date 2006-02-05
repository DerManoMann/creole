<?php

require_once 'creole/CreoleBaseTest.php';

/**
 * Tests for the Connection class.
 * 
 * - test connection / exceptions
 * - test transactions
 * - test the simple query functions
 * - test the LIMIT SQL modification (that must happen in subclasses).
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.8 $
 */
class ConnectionTest extends CreoleBaseTest {
    
    /**
     * The database connection.
     * @var Connection
     */
    protected $conn;
    
    /**
     * Construct the class.  This is called before every test (method) is invoked.
     */
    public function __construct() {
        $this->conn = DriverTestManager::getConnection();        
    }
     
    public function setUp() {
        // Perform any actions here that don't rely on existence of class variables        
    }
    
    public function tearDown() {
        //DriverTestManager::restore();
    }       
    
    /**
     * Test update count for insert, update, and delete.
     */ 
    public function testGetUpdateCount() {
        DriverTestManager::restore();
        
        $exc = DriverTestManager::getExchange('ConnectionTest.getUpdateCount.UPDATE');
        $count = $this->conn->executeUpdate($exc->getSql());        
        $rs = $this->conn->executeQuery("SELECT * FROM products WHERE ProductID = 2");
        $this->assertEquals((int) $exc->getResult(), $count);
        
        
        $exc = DriverTestManager::getExchange('ConnectionTest.getUpdateCount.DELETE');
        $count = $this->conn->executeUpdate($exc->getSql());        
        $this->assertEquals((int) $exc->getResult(), $count);
        
        $exc = DriverTestManager::getExchange('ConnectionTest.getUpdateCount.INSERT');
        $count = $this->conn->executeUpdate($exc->getSql());
        $this->assertEquals((int) $exc->getResult(), $count);
                
        // zap db since we modified it
        DriverTestManager::restore();
    }
    
    /**
     * Test for correct behavior in turning on or off auto-commit.
     * This function also tests recordset::getRecordCount(), as a side-effect.
     */
    public function testSetAutoCommit() {
        
        // by default auto-commit is TRUE.
        $exch = DriverTestManager::getExchange('RecordCount');
        $count_sql = $exch->getSql();
        $rs = $this->conn->executeQuery($count_sql, ResultSet::FETCHMODE_NUM);
        $rs->next();
        $total = $rs->getInt(1);
        $this->assertEquals((int) $exch->getResult(), $total);        
        
        // now begin a transaction 
        $this->conn->setAutoCommit(false);
        $this->assertFalse($this->conn->getAutoCommit(), "getAutoCommit() did not return FALSE after just having set it to false.");

        $exch = DriverTestManager::getExchange('ConnectionTest.setAutoCommit.DELTRAN1');
        $deleted1 = $this->conn->executeUpdate($exch->getSql());
        
        $exch = DriverTestManager::getExchange('ConnectionTest.setAutoCommit.DELTRAN2');
        $deleted2 = $this->conn->executeUpdate($exch->getSql());
        
        $total_should_be = $total - ($deleted1 + $deleted2);
                              
        $this->conn->setAutoCommit(true); // will implicitly commit the transaction
        
        $this->expectWarning("Changing autocommit in mid-transaction");
        
        // compare the actual total w/ what we expect
        $rs = $this->conn->executeQuery($count_sql, ResultSet::FETCHMODE_NUM);
        $rs->next();
        $new_actual_total = $rs->getInt(1);       
        
        $this->assertEquals($total_should_be, $new_actual_total, 0, "Failed to find correct num of records after implicit transaction commit using setAutoCommit(TRUE).");
    }
    
    /**
     * Tests explicit commit function.
     */
    public function testCommit() {
                     
        
    // by default auto-commit is TRUE.
        $exch = DriverTestManager::getExchange('RecordCount');
        $count_sql = $exch->getSql();
        $rs = $this->conn->executeQuery($count_sql, ResultSet::FETCHMODE_NUM);
        $rs->next();
        $total = $rs->getInt(1);
        // $this->assertEquals((int) $exch->getResult(), $total);        
        
        // now begin a transaction 
        $this->conn->setAutoCommit(false);
        
        $exch = DriverTestManager::getExchange('ConnectionTest.setAutoCommit.DELTRAN1');
        $deleted1 = $this->conn->executeUpdate($exch->getSql());
        
        $exch = DriverTestManager::getExchange('ConnectionTest.setAutoCommit.DELTRAN2');
        $deleted2 = $this->conn->executeUpdate($exch->getSql());
        
        $total_should_be = $total - ($deleted1 + $deleted2);
                              
        $this->conn->commit();
        
        // compare the actual total w/ what we expect
        $rs = $this->conn->executeQuery($count_sql, ResultSet::FETCHMODE_NUM);
        $rs->next();
        $new_actual_total = $rs->getInt(1);       
        
        $this->assertEquals($total_should_be, $new_actual_total, 0, "Failed to find correct num of records after explicit transaction commit.");            
        
        $this->conn->setAutoCommit(true);
                
    }
    
    public function testRollback() {
        
        $exch = DriverTestManager::getExchange('RecordCount');
        $count_sql = $exch->getSql();
        $rs = $this->conn->executeQuery($count_sql, ResultSet::FETCHMODE_NUM);
        $rs->next();
        $total = $rs->getInt(1);
        // $this->assertEquals((int) $exch->getResult(), $total);
        
        $this->conn->setAutoCommit(false);
        
        // not sure exactly how to test this yet ...
        $exch = DriverTestManager::getExchange('ConnectionTest.setAutoCommit.DELTRAN1');
        $deleted1 = $this->conn->executeUpdate($exch->getSql());
        
        $exch = DriverTestManager::getExchange('ConnectionTest.setAutoCommit.DELTRAN2');
        $deleted2 = $this->conn->executeUpdate($exch->getSql());        
        
        $this->conn->rollback();
        
        // compare the actual total w/ what we expect
        $rs = $this->conn->executeQuery($count_sql, ResultSet::FETCHMODE_NUM);
        $rs->next();
        $new_actual_total = $rs->getInt(1);       
        
        $this->assertEquals($total, $new_actual_total, 0, "Failed to find correct (same) num of records in table after rollback().");
        
        $this->conn->setAutoCommit(true);
        
    }
 
    /**
     * Test the applyLimit() method.  By default this method will not modify the values provided.
     * Subclasses must override this method to test for appropriate SQL modifications.
     */
    public function testApplyLimit() {        
        
        $sql = "SELECT * FROM sampletable WHERE category = 5";        
        $offset = 5;
        $limit = 50;
        
        $this->conn->applyLimit($sql, $offset, $limit);
        
        $this->assertEquals("SELECT * FROM sampletable WHERE category = 5 LIMIT 50 OFFSET 5", $sql);        
    }
    
}
