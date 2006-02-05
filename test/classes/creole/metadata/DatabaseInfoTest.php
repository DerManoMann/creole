<?php

require_once 'creole/CreoleBaseTest.php';

/**
 * Tests for the DataseInfo class.
 *
 * -
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.2 $
 */
class DatabaseInfoTest extends CreoleBaseTest {

    /**
     * The database connection.
     * @var Connection
     */
    protected $conn;

    /**
     * @var DatabaseInfo
     */
    protected $dbi;

    public function setUp() {
    }

    /**
     * Construct the class.  This is called before every test (method) is invoked.
     */
    public function __construct() {
        $this->conn = DriverTestManager::getConnection();
        $this->dbi = $this->conn->getDatabaseInfo();
    }

    /**
     * Make sure at least "products" table is in table list.
     */
    public function testGetTables() {
        $tables = $this->dbi->getTables();
        $this->assertTrue(count($tables) >= 1, "Expected at least one table ('products')from getTables() call.");
    }

    /** Test getting the products table */
    public function testGetTable() {
        $products = $this->dbi->getTable("products");
        $products2 = $this->dbi->getTable("Products");
        $this->assertEquals($products, $products2, 0, "Expected getTable() to be case-insensitive.");
    }
}
