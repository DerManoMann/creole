<?php

require_once 'creole/CreoleBaseTest.php';

/**
 * Tests for the Statement class.
 * 
 * - 
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.6 $
 */
class StatementTest extends CreoleBaseTest {
    
    /**
     * The database connection.
     * @var Connection
     */
    protected $conn;

    public function setUp() {
        DriverTestManager::restore();
    }
    
    /**
     * Construct the class.  This is called before every test (method) is invoked.
     */
    public function __construct() {
        $this->conn = DriverTestManager::getConnection();
    }             
    
    public function testSetLimit() {
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $stmt = $this->conn->createStatement();
        $stmt->setLimit(10);        
        $rs = $stmt->executeQuery($exch->getSql(),ResultSet::FETCHMODE_NUM);        
        $this->assertEquals(10, $rs->getRecordCount());
    }
    
    public function testSetOffset() {
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $stmt = $this->conn->createStatement();
        $stmt->setLimit(10);
        $stmt->setOffset(5);
        $rs = $stmt->executeQuery($exch->getSql(),ResultSet::FETCHMODE_NUM);
        
        $rs->next();
        
        $this->assertEquals(6, $rs->getInt(1));
        
        $rs->close();
        
        // test setting offset w/ no limit        
        $stmt->setLimit(0);
        $stmt->setOffset(6);
        $rs = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);        
        $rs->next();        
        $this->assertEquals(7, $rs->getInt(1));
        
        // try changing the offset info
        $stmt->setLimit(10);
        $stmt->setOffset(4);
        $rs = $stmt->executeQuery($exch->getSql(),ResultSet::FETCHMODE_NUM);
        
        $rs->next();
        
        $this->assertEquals(5, $rs->getInt(1), 0, "Expected new first row to have changed after changing offset.");
        
        $stmt->close();
    }        
    

    /**
     * @todo -c Implement 
     */
    public function testGetMoreResults() {
        
        // coming sooon..
    }
    
    public function testExecuteQuery() {                       
        $exch = DriverTestManager::getExchange('StatementTest.executeQuery');
        $stmt = $this->conn->createStatement();
        $rs = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        
        $this->assertEquals(1, $rs->getInt(1));
        
        $rs->close();
        
        // make sure that getupdatecount returns null
        
        $this->assertTrue( ($stmt->getUpdateCount() === null), "Expected getUpdateCount() to return NULL since last statement was a query.");
        $stmt->close();
    }
    
    public function testExecuteUpdate() {
        $exch = DriverTestManager::getExchange('StatementTest.executeUpdate');
        $stmt = $this->conn->createStatement();
        $stmt->executeUpdate($exch->getSql());        
        $this->assertEquals(1, $stmt->getUpdateCount());        
        $this->assertTrue( ($stmt->getResultSet() === null), "Expected getResultSet() to return NULL since last statement was an update.");
        $stmt->close();        
    }
    
     public function testExecute() {    
        
        $exch = DriverTestManager::getExchange('StatementTest.executeUpdate');
        $stmt = $this->conn->createStatement();
        $res = $stmt->execute($exch->getSql());
        $this->assertFalse($res, "Expected resulst of execute() to be FALSE because an update statement was executed (this is to match JDBC return values).");
        $this->assertEquals(1, $stmt->getUpdateCount());
        
        $exch = DriverTestManager::getExchange('StatementTest.executeQuery');
        $stmt = $this->conn->createStatement();
        $res = $stmt->execute($exch->getSql());
        $this->assertTrue($res, "Expected resulst of execute() to be TRUE because a select query was executed (this is to match JDBC return values).");
        $this->assertTrue($stmt->getResultSet() instanceof ResultSet, "Expected to be able to getResultSet() after call to execute() w/ SELECT query.");

        $stmt->close();
    }
    
}
