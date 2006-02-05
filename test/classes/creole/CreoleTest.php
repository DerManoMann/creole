<?php

require_once 'creole/CreoleBaseTest.php';
require_once 'creole/Creole.php';

/**
 * Unit tests for Creole driver manager.
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Id: CreoleTest.php,v 1.2 2004/03/04 16:01:00 hlellelid Exp $
 * @package creole
 */
class CreoleTest extends CreoleBaseTest {
    
    const ALL_KEYS = 'check-all-keys';
    
    public function setUp() {
    
    }
    
    /**
     * Checks to make sure value at $dsninfo[$key] = $key.
     * 
     * E.g. given the URL phptype://username@hostspec, you want to make sure that
     * $dsninfo['phptype'] = 'phptype', etc.
     *  
     * @param array $dsninfo The parsed DSN to check
     * @param array $include array of keys to check for, optionally you may specify key => value
     */
    private function checkDSN($dsninfo, $include) {        
        foreach($include as $k => $v) {
            if (!is_numeric($k)) { // array(..., 'port' => 110) syntax is being used
                $this->assertEquals($v, $dsninfo[$k]);
            } else {
                $this->assertEquals($v, $dsninfo[$v]);
            }            
        }        
    }
    
    /**
     * Test different URL DSNs to make sure they are being parsed correctly.
     */
    public function testParseDSN() {
        
        $dsn = "phptype://username:password@protocol+hostspec:110//usr/db_file.db?param1=value1&param2=value2";
        $dsninfo = Creole::parseDSN($dsn);
        
        $this->checkDSN($dsninfo, array('phptype', 'username', 'password', 'protocol', 'hostspec', 'port'=> 110,
                                         'database' => '/usr/db_file.db', 'param1'=>'value1','param2'=>'value2',
                            ));

        $dsn = 'phptype://username:password@hostspec/C:\path\to\dbfile.db';
        $this->checkDSN(Creole::parseDSN($dsn), array('phptype', 'username', 'password', 'hostspec', 'database' => 'C:\path\to\dbfile.db'));
        
        $dsn = "phptype://username:password@hostspec/database";
        $this->checkDSN(Creole::parseDSN($dsn), array('phptype', 'username', 'password', 'hostspec', 'database'));
                
        $dsn = "phptype://username:password@hostspec";
        $this->checkDSN(Creole::parseDSN($dsn), array('phptype', 'username', 'password', 'hostspec'));

        $dsn = "phptype://username@hostspec";
        $this->checkDSN(Creole::parseDSN($dsn), array('phptype', 'username', 'hostspec'));

        $dsn = "phptype://hostspec/database";
        $this->checkDSN(Creole::parseDSN($dsn), array('phptype', 'hostspec', 'database'));

        $dsn = "phptype";
        $this->checkDSN(Creole::parseDSN($dsn), array('phptype'));
                                
    }
    
    /**
     * Test to account for invalid phptype.
     */ 
    public function testGetConnection() {
        try {
            $driver = Creole::getConnection('nodriver://hostname/dbname');
            $this->fail("Expected SQLException to be thrown by attempt to connect to unregistered driver type.");
        } catch (SQLException $e) {
            $this->expectException("No driver has been registered to handle connection type: nodriver", $e);
        }
    }
    
    /**
     * Test to make sure that registered drivers can be included and that
     * appropriate exceptions are thrown if they do not exist or do not
     * implement creole.Connection.
     */
    public function testRegisterDriver() {        
        
        // A good connection
        Creole::registerDriver('mycon', 'example.MyDriverConnection');
        $driver = Creole::getConnection('mycon://hostname/dbname');
        $this->assertEquals("MyDriverConnection", get_class($driver));                
                                
        // A file that doesn't exist
        Creole::registerDriver('mycon', 'example.NonExistConnection');
        try {
            $driver = Creole::getConnection('mycon://hostname/dbname');
            $this->fail("Expected SQLException to be thrown by attempt to use driver that does not exist.");
        } catch (SQLException $e) {
            $this->expectException("Unable to load driver class", $e);
        }
        
        // A class that doesn't match file name that doesn't exist
        Creole::registerDriver('mycon', 'example.MisnamedDriverConnection');
        try {
            $driver = Creole::getConnection('mycon://hostname/dbname');
            $this->fail("Expected SQLException to be thrown by attempt to use a driver class that was not defined in file of same name.");
        } catch (SQLException $e) {
            $this->expectException("Unable to find loaded class: MisnamedDriverConnection", $e);
        }
        
        // A class that doesn't implement creole.Connection
        include_once ('example/BadDriverConnection.php');
        Creole::registerDriver('badcon', 'BadDriverConnection'); // also testing alternate method of including stuff
        try {
            $driver = Creole::getConnection('badcon://hostname/dbname');
            $this->fail("Expected SQLException to be thrown by attempt to use driver that did not implement Connection.");
        } catch (SQLException $e) {
            $this->expectException("Class does not implement creole.Connection interface: BadDriverConnection", $e);
        }
        
    }

    /**
     * Test "catchall" driver registration.
     */
    public function testRegisterCatchallDriver() {
        Creole::registerDriver('my', 'example.MyDriverConnection');
        Creole::registerDriver('my2', 'example.MyDriver2Connection');
        Creole::registerDriver('*', 'example.MyCatchallConnection');
        
        $driver = Creole::getConnection('my://hostname/dbname');
        
        $this->assertEquals("MyCatchallConnection", get_class($driver));         
        $this->assertEquals("MyDriverConnection", get_class($driver->driver)); 
        
        $driver2 = Creole::getConnection('my2://hostname/dbname');
        $this->assertEquals("MyCatchallConnection", get_class($driver2));
        $this->assertEquals("MyDriver2Connection", get_class($driver2->driver)); 
        
        // do this or break the rest of the code in the test suite :)
        Creole::deregisterDriver('*');    
    }
    
    /**
     * Test to make sure that a de-registered driver cannot
     * be used.
     */
    public function testUnregisterDriver() {
        Creole::deregisterDriver('mysql');
        try {
            $driver = Creole::getConnection('mysql://hostname/dbname');
            $this->fail("Expected SQLException to be thrown by attempt to connect to unregistered driver type.");
        } catch (SQLException $e) {
            $this->expectException("No driver has been registered to handle connection type: mysql", $e);
        }
        
        // now put it back :)
        Creole::registerDriver('mysql', 'creole.drivers.mysql.MySQLConnection');        
    }
    
}