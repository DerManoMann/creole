<?php

require_once 'creole/CreoleBaseTest.php';
include_once 'creole/PreparedStatementTest.php';

/**
 * Tests for the ResultSet class.
 * 
 * - test all the scrolling functions for correct behavior & return values
 * - test limit/offset (this is especially important for drivers that emulate this)
 * - test fetchmodes & test COMPAT_ASSOC_LOWER option on FETCHMODE_ASSOC
 * - test the field getters for correct formatting & correct exception throwing
 * - test close method
 * 
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.19 $
 */
class ResultSetTest extends CreoleBaseTest {
    
    protected $conn;    
    
    /**
     * Re-initialize the database.
     * 
     * We only need to do this in setUp() method -- not in every invocation of this class --
     * since the ResultSet methods do not modify the db.
     */    
    public function setUp() {
        DriverTestManager::restore();
    }
    
    public function __construct() {
        $this->conn = DriverTestManager::getConnection();
    }   
    
    /**
     * Initialize the default resultset.
     * Not all methods need this initialized.
     */
    protected function allRs() {
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        return $rs;
    }            
    
    /**
     * Test the getRecordCount method.  Note that this will not work w/
     * unbuffered result sets ... e.g. I think Oracle.
     */
    public function testGetRecordCount() {
        // SELECT COUNT(*) ...
        $exch1 = DriverTestManager::getExchange('RecordCount');
        $rs = $this->conn->executeQuery($exch1->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        $expected = $rs->getInt(1);
        
        // SELECT * ...
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $rs = $this->conn->executeQuery($exch->getSql());
        $this->assertEquals($expected, $rs->getRecordCount());
    }
    
    public function testFetchmodeNum() {
    
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        $fields = $rs->getRow();
        $this->assertTrue( array_key_exists("0", $fields) ); 
        $this->assertTrue( !array_key_exists("ProductID", $fields) );
        $rs->close();
                                    
    }    

    public function testFetchmodeAssoc() {   
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');                
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $fields = $rs->getRow();
        $keys = array_keys($fields);
        $this->assertTrue( !array_key_exists("0", $fields), "Expected not to find '0' in fields array." );
        $this->assertEquals( "productid" , $keys[0], 0, "Expected to find lcase column name in field array.");
        $rs->close();
    }                   
    
    /**
     * Test an ASSOC fetch with a connection that does not have the Creole::COMPAT_ASSOC_LOWER flag set.
     */
    public function testFetchmodeAssocNoChange() {
    
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        
        $conn2 = Creole::getConnection(DriverTestManager::getDSN());        
        DriverTestManager::initDb($conn2);
        
        $rs = $conn2->executeQuery($exch->getSql(), ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $keys = array_keys($rs->getRow());
        $this->assertEquals("ProductID", $keys[0], 0, "Expected to find mixed-case column name.");
        $rs->close();
        
        // do NOT close the connection; in many cases both COnnection objects will share
        // the same db connection
                
    }
    
    /**
     * Test next() and bounded result sets.
     * We test to make sure that next() will loop until the end.
     */
    public function testNext() {
        $rs = $this->allRs();
        $i=0;
        while($rs->next()) $i++;
        $this->assertEquals($rs->getRecordCount(), $i);
        
        $rs->close();
    }
    
    /**
     * Ensures that results are no longer available after
     * closing a resultset.
     */
    public function testClose() {
        $rs = $this->allRs();
        $rs->next();
        $rs->close();
        try {
            $rs->get(1);
            $this->fail("Expected SQLException to be thrown for invalid column after closing ResultSet.");
        } catch (SQLException $e) {
            $this->expectException("Invalid resultset column", $e);
        }
    }
    
    /**
     * Test behavior of seek().  Note that the return results of seek
     * are not reliable for determining whether a cursor position exists.
     */
    public function testSeek() {
        $rs = $this->allRs();
        
        $rs->seek(0);
        $rs->next();
        
        $this->assertEquals(1, $rs->getInt(1));
        $rs->seek(3);
        $this->assertEquals(1, $rs->getInt(1), 0, "Expected to still find same value for get(1), since seek() isn't supposed to load row.");
        $rs->next();
        $this->assertEquals(4, $rs->getInt(1), 0, "Expected next() to now fetch 4 after call to seek(3)");
        
        $rs->close();
    }
    
    
    public function testIsBeforeFirst() {        
        $rs = $this->allRs();
        // before calling next() we can expect RS to be before first
        $this->assertTrue($rs->isBeforeFirst());       
        
        $rs->close();
    }
    
    public function testIsAfterLast() {
        $rs = $this->allRs();
        while($rs->next()); // advance to end        
        $this->assertTrue($rs->isAfterLast());
        
        $rs->close();
    }
    
    // these are not scrolling functions:
    
    public function testBeforeFirst() {
        $rs = $this->allRs();
        for($i=0;$i<10;$i++) { // advance a few positions
            $rs->next();
        }
        
        $rs->beforeFirst();
        $this->assertTrue($rs->isBeforeFirst());
        
        $rs->close();
    }
    
    
    public function testAfterLast() {
        $rs = $this->allRs();
        for($i=0;$i<10;$i++) { // advance a few positions
            $rs->next();
        }        
        $rs->afterLast();
        $this->assertTrue($rs->isAfterLast());    
        
        $rs->close();
    }
    
    //
    // scrolling functions -- do not work w/ all RDBMS, so must be overridden when applicable
    // 
    
    public function testPrevious() {
    
        $rs = $this->allRs();
        
        // advance to the fifth record, which will have ProductID of 5
        for($i=0;$i<5;$i++) $rs->next();
        
        $this->assertEquals(5, $rs->getInt(1));
        
        $rs->previous();
        
        $this->assertEquals(4, $rs->getInt(1));
                
        // now keep going back until false
        while($rs->previous());
        
        $this->assertTrue($rs->isBeforeFirst());
        
        $rs->close();
    }
    
    public function testRelative() {
    
        $rs = $this->allRs();
        
        $rs->next(); // advance one record
            
        // move ahead 5 spaces
        $rs->relative(5);
        $this->assertEquals(6, $rs->getInt(1));
        
        $rs->relative(-2);
        $this->assertEquals(4, $rs->getInt(1));
        
        
        $res = $rs->relative(200);
        $this->assertTrue($rs->isAfterLast());
        $this->assertFalse($res, "relative() should return false if offset after end of recordset");
        //$this->expectWarning('Offset after end of recordset', $rs);
        
        $res = $rs->relative(-200);
        $this->assertTrue($rs->isBeforeFirst());
        $this->assertFalse($res, "relative() should return false if offset before start of recordset");
        //$this->expectWarning('Offset before start of recordset', $rs);
        
        $rs->relative(2);
        $this->assertEquals(2, $rs->getInt(1));        
        
        $rs->close();
    }
    
    public function testAbsolute() {        
        $rs = $this->allRs();
         // advance to the fifth record, which will have ProductID of 5
        $rs->absolute(5);
        $this->assertEquals(5, $rs->getInt(1));
        
        $rs->absolute(50);
        $this->assertEquals(50, $rs->getInt(1));
        
        $res = $rs->absolute(300);
        $this->assertTrue($rs->isAfterLast());
        $this->assertFalse($res, "absolute() should return false if pos is after end of recordset"); // returns false if offset is after last or before first
        //$this->expectWarning('Offset after end of recordset', $rs);
        
        $res = $rs->absolute(0);
        $this->assertTrue($rs->isBeforeFirst());
        $this->assertFalse($res, "absolute() should return false if offset is before start of recordset"); // returns false if offset is after last or before first
        //$this->expectWarning('Offset before start of recordset', $rs);
        
        $res = $rs->absolute(-2);
        $this->assertTrue($rs->isBeforeFirst());
        $this->assertFalse($res, "absolute() should return false if offset is before start of recordset"); // returns false if offset is after last or before first
        //$this->expectWarning('Offset before start of recordset', $rs);
        
        $rs->close();            
    }
    
    public function testFirst() {    
        $rs = $this->allRs();
        
        $exch = DriverTestManager::getExchange('ResultSetTest.MIN_ID');
        $minRs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $minRs->next();
        $min = $minRs->get(1);
        
        $rs->first();
        $this->assertEquals($min, $rs->get(1));
        
        $rs->close();
    }
    
    public function testLast() {
        $rs = $this->allRs();
              
        $exch = DriverTestManager::getExchange('ResultSetTest.MAX_ID');
        $maxRs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $maxRs->next();
        $max = $maxRs->get(1);
        
        $rs->last();
        $this->assertEquals($max, $rs->get(1));
        
        $rs->close();
    }    
    
    /**
     * This test is primarily to test emulated LIMIT/OFFSET. 
     * 
     * It will, of course, test the natively supported LIMIT/OFFSET, but
     * the real potential for issues lies in the drivers that emulate these.
     * 
     * This class only uses forward-scrolling cursor functions.
     * @see testLimitScrollBackwards
     */
    public function testLimit() {
        
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $stmt = $this->conn->createStatement();
        $stmt->setLimit(10);
        $stmt->setOffset(5);
         
        // 1) make sure contains right number of rows
            $rs1 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $count = 0;
            while($rs1->next()) $count++;            
            $this->assertEquals(10, $count, 0, "LIMITed resultset contains wrong number of rows.");
            $rs1->close();
            unset($rs1);
        
        // 2) make sure that first record is the correct one
            // using next()
            $rs2 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $rs2->next();
                // first() relative() and absolute() handled by testLimitScrollBackwards()
            $this->assertEquals(6, $rs2->getInt(1), 0, "LIMITed resultset starts on the wrong row.");
            $rs2->close();
            unset($rs2);
        
        // 3) make sure that the last record is the correct one.
            $rs3 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            while($rs3->next()) { $last = $rs3->getInt(1); }
            $this->assertEquals(15, $last, 0, "LIMITed resultset ends on the wrong row.");
            $rs3->close();
            unset($rs3);
            
            $rs4 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $rs4->last();
            $this->assertEquals(15, $rs4->getInt(1), 0, "LIMITed resultset ends on the wrong row.");
            $rs4->close();
            unset($rs4);     
                                   
        // 4) make sure that the relative() and absolute() (forward) method will report appropriate end
           
            $rs5 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $res = $rs5->absolute(11);
            //$this->expectWarning('Offset after end of recordset',$rs5);
            $this->assertFalse($res, "absolute() should return false when after end of resultset");
            $rs5->close();
            unset($rs5);     
            
            $rs6 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $res = $rs6->relative(11);
            $this->assertFalse($res, "relative() should return false when after end of resultset");
            //$this->expectWarning('Offset after end of recordset', $rs6);
            $rs6->close();
            unset($rs6);
            
        $stmt->close();
    }
    
    /**
     * Continues LIMIT tests, but using backwards-scrolling methods.
     * 
     * Some RDBMS drivers don't support backwards scrolling; they'll need
     * to override this method.
     */
    public function testLimitScrollBackwards() {
    
        $exch = DriverTestManager::getExchange('ResultSetTest.ALL_RECORDS');
        $stmt = $this->conn->createStatement();
        $stmt->setLimit(10);
        $stmt->setOffset(5);
                        
        // using next()
        $rs2 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs2->first();            
        $this->assertEquals(6, $rs2->getInt(1), 0, "LIMITed resultset starts on the wrong row.");
        $rs2->close();
        unset($rs2);

        $rs3 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        for($i=0;$i<3;$i++) $rs3->next(); // move ahead 3 spaces            
        $res = $rs3->relative(-4);
        $this->assertFalse($res, "relative() should return false when before start of resultset");
        //$this->expectWarning('Offset before start of recordset', $rs3);
        $rs3->close();          
        unset($rs3);
        
        $rs4 = $stmt->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        for($i=0;$i<3;$i++) $rs4->next(); // move ahead 3 spaces            
        $res = $rs4->absolute(-1);
        $this->assertFalse($res, "absolute() should return false when before start of resultset");
        //$this->expectWarning('Offset before start of recordset', $rs4);
        $rs4->close();       
        unset($rs4);
        
        $stmt->close();
    }
    
    //
    // column accessors -- many of these will be overridden in driver classes so
    // that derived values can be checked against native values in DB.
    //
    
    public function testGet() {   
        $exch = DriverTestManager::getExchange('ResultSetTest.SINGLE_RECORD');
        $rs = $this->conn->executeQuery(sprintf($exch->getSql(), 1), ResultSet::FETCHMODE_NUM);
        $rs->next();         
        $this->assertEquals(1, $rs->getInt(1));
        
        $rs->close();
    }

    public function testGetArray() {
        // coming soon
    }
    
    public function testGetBoolean() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getBoolean.FALSE');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();        
        $this->assertTrue($rs->getBoolean(1) === false, "Expected answer to be false, was: " . $rs->getBoolean(1));
        
        // avoid using absolute() or relative() because not all drivers support it.
        $exch = DriverTestManager::getExchange('ResultSetTest.getBoolean.TRUE');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        $this->assertTrue($rs->getBoolean(1) === true);
        try {
            $rs->getBoolean("productid");
            $this->fail("Expected SQLException to be thrown for invalid column.");
        } catch (SQLException $e) {
            $this->expectException("Invalid resultset column", $e);
        }
        
        $rs->close();
    }
    
    public function getBlob() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getBlob');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        $b = $rs->getBlob(1);
        $rs->close();
        return $b;
    }

    public function getClob() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getClob');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        $c = $rs->getClob(1);
        $rs->close();
        return $c;
    }
    
    /**
     * This function depends on ability to set Blob values -- so 
     * PreparedStatement::setBlob() is also implicitly tested.
     */
    public function testGetBlob() {    
        $pst = new PreparedStatementTest();
        $b1 = $pst->createBlob();
        $pst->setBlob($b1);
        
        $b2 = $this->getBlob();
        $this->assertEquals(strlen($b1->getContents()), strlen($b2->getContents()), 0, "BLOB lengths do not match.");
        $this->assertEquals(md5($b1->getContents()), md5($b2->getContents()), 0, "BLOB contents do not match.");
    }
    
    /**
     * This function depends on ability to set Blob values -- so 
     * PreparedStatement::setBlob() is also implicitly tested.
     */
    public function testGetClob() {        
        $pst = new PreparedStatementTest();
        $b1 = $pst->createClob();
        $pst->setClob($b1);
        
        $b2 = $this->getClob();        
        $this->assertEquals(strlen($b1->getContents()), strlen($b2->getContents()), 0, "CLOB lengths do not match.");
        $this->assertEquals(md5($b1->getContents()), md5($b2->getContents()), 0, "CLOB contents do not match.");
    }

    public function testGetDate() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getDate');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        
        $result_ts = strtotime($exch->getResult());        
        $ts = (int) $rs->getDate(1, "U");
        
        $this->assertEquals($result_ts, $ts);        
        
        $this->assertEquals(strftime("%x", $result_ts), $rs->getDate(1, "%x"));        
                
        try {
            $rs->getDate("orderdate");
            $this->fail("Expected SQLException to be thrown for invalid column.");
        } catch (SQLException $e) {
            $this->expectException("Invalid resultset column", $e);
        }
        
        $rs->close();

        // try w/ invalid date
        try {
            $exch = DriverTestManager::getExchange('ResultSetTest.getString');
            $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $rs->next();
            $rs->getDate(1);
            $this->fail("Expected SQLException to be thrown for bad date type.");
        } catch (SQLException $e) {
            $this->expectException("Unable to convert value", $e);
        }
        
        $rs->close();
                
    }        

    public function testGetFloat() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getFloat');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        
        $exp_val = (float) $exch->getResult();
        
        $this->assertEquals($exp_val, $rs->getFloat(1));
        
        try {
            $rs->getFloat("UnitPrice");
            $this->fail("Expected SQLException to be thrown for invalid column.");
        } catch (SQLException $e) {
            $this->expectException("Invalid resultset column", $e);
        }
        
        $rs->close();
    }
        
    public function testGetInt() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getInt');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();    
        
        $exp_val = (int) $exch->getResult();
        
        $this->assertEquals($exp_val, $rs->getInt(1));        
        
        try {
            $rs->getInt("UnitsOnOrder");
            $this->fail("Expected SQLException to be thrown for invalid column.");
        } catch (SQLException $e) {
            $this->expectException("Invalid resultset column", $e);
        }
        
        $rs->close();
    }

    public function testGetString() {
        $exch = DriverTestManager::getExchange('ResultSetTest.getString');
        $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
        $rs->next();
        
         $exp_val = $exch->getResult();
        
        $this->assertEquals($exp_val, $rs->getString(1));
        
        try {
            $rs->getString("ProductName");
            $this->fail("Expected SQLException to be thrown for invalid column.");
        } catch (SQLException $e) {
            $this->expectException("Invalid resultset column", $e);
        }
        
        $rs->close();
    }

    public function testGetTime() {        
        
        // coming soon ... 
        
        // try w/ invalid time
        try {
            $exch = DriverTestManager::getExchange('ResultSetTest.getString');
            $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $rs->next();
            $rs->getTime(1);
            $this->fail("Expected SQLException to be thrown for bad date type.");
        } catch (SQLException $e) {
            $this->expectException("Unable to convert value", $e);
        }

    }
    
    public function testGetTimestamp() {
        
        // coming soon ...
                
        // try w/ invalid timestamp
        try {
            $exch = DriverTestManager::getExchange('ResultSetTest.getString');
            $rs = $this->conn->executeQuery($exch->getSql(), ResultSet::FETCHMODE_NUM);
            $rs->next();
            $rs->getTimestamp(1);
            $this->fail("Expected SQLException to be thrown for bad date type.");
        } catch (SQLException $e) {
            $this->expectException("Unable to convert value", $e);
        }

    }
    
    /**
     * Make sure that get() and getString() are returning properly rtrimmed results.
     */
    public function testTrimmedGet() {
        
        $conn = Creole::getConnection(DriverTestManager::getDSN(), Creole::COMPAT_RTRIM_STRING);
		DriverTestManager::initDb($conn);
		
		$exch = DriverTestManager::getExchange('ResultSetTest.setString.RTRIM');
        $stmt = $conn->prepareStatement($exch->getSql());
        $stmt->setString(1, "TEST    ");
        $stmt->setInt(2, 1);
        $stmt->executeUpdate();
        $stmt->close();
		     
        $exch = DriverTestManager::getExchange('ResultSetTest.getString.RTRIM');
        $stmt = $conn->prepareStatement($exch->getSql());
        $stmt->setInt(1, 1);
        $rs = $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
        $rs->next();
        $this->assertEquals("TEST", $rs->getString(1));
        
        $stmt->close();
        $rs->close();
    }
	
	/**
     * Make sure that get() and getString() are returning properly rtrimmed results.
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
        $this->assertEquals($str, $rs->getString(1));
        
        $stmt->close();
        $rs->close();
    }
}
