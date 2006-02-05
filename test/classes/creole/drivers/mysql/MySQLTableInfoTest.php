<?php

require_once 'creole/metadata/TableInfoTest.php';

/**
 * MySQLDatabaseInfo tests.
 *
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.1 $
 */
class MySQLTableInfoTest extends TableInfoTest {

    public function testTableVendorSpecificInfo() {
        $table = $this->conn->getDatabaseInfo()->getTable("vendor");
        $info = $table->getVendorSpecificInfo();

        //primitive check if vendor stuff is loaded, it's not clear
        //if it makes sense to test more information
        //...this is really weird, the line below crashes on my PHP 5.0.3, windows box
        //$this->assertEquals($info['Type'], 'MyISAM');
        //...and this one creashes also!
        //$this->assertTrue($info['Type'] == 'MyISAM');

        $res = $info['Type'] == 'MyISAM';
        $this->assertTrue($res);
    }

    public function testIndexVendorSpecificInfo() {
        $table = $this->conn->getDatabaseInfo()->getTable("vendor");
        $index = $this->findIndex($table, 'Content');
        $info = $index->getVendorSpecificInfo();

        //primitive check if vendor stuff is loaded
        $this->assertEquals($info['Index_type'], 'FULLTEXT');
    }

    public function testColumnVendorSpecificInfo() {

        $table = $this->conn->getDatabaseInfo()->getTable("vendor");
        $col = $table->getColumn('id');

        $this->assertTrue($col->isAutoIncrement());
    }

}
