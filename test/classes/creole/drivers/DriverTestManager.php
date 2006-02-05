<?php

include_once 'creole/Creole.php';
include_once 'creole/util/sql/SQLStatementExtractor.php';

/**
 * A static class to help out with executing tests for each driver.
 *
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @version $Revision: 1.16 $
 */
class DriverTestManager {

    protected static $dsn;
    protected static $conn;

    protected static $schemaStatements;
    protected static $dataStatements;

    protected static $domExchanges;
    protected static $xpExchanges;

    // Look for driver versions of the following classes, if found, add them.
    protected static $driverClasses = array('Connection', 'ResultSet', 'PreparedStatement', 'CallableStatement', 'IdGenerator', 'Statement', 'DatabaseInfo', 'TableInfo');

    public static function setDriverClasses($classes) {
        self::$driverClasses = $classes;
    }

    public static function setDSN($dsn) {
        if (is_string($dsn)) {
            $dsn = Creole::parseDSN($dsn);
        }
        self::$dsn = $dsn;
    }

    public static function getDSN() {
        return self::$dsn;
    }

    public static function getConnection() {
        return self::$conn;
    }

    public static function connect() {
        self::$conn = Creole::getConnection(self::$dsn, Creole::COMPAT_ASSOC_LOWER);
    }

    public static function init() {
        self::connect();
        self::loadStatements();
        self::initDb(self::$conn);
        if (self::$domExchanges === null) {
            self::$domExchanges = new DomDocument();
            self::$domExchanges->load(CREOLE_TEST_BASE . '/etc/exchanges.xml');
            self::$xpExchanges = new DomXPath(self::$domExchanges);
        }
    }

    /**
     * Call this method to destroy and re-create the tables in the db.
     */
    public static function restore() {
        $dsn = self::$conn->getDSN();
        $flags = self::$conn->getFlags();
        self::$conn->close();
        self::$conn->connect($dsn, $flags);
        self::initDb(self::$conn);
    }

    /**
     * Loads & parses the schema SQL files.
     */
    protected static function loadStatements()
    {
        $schema = CREOLE_TEST_BASE . '/etc/db/sql/' . self::$dsn['phptype'] . '/creoletest-schema.sql';
        $data = CREOLE_TEST_BASE . '/etc/db/sql/' . self::$dsn['phptype'] . '/creoletest-data.sql';
        self::$schemaStatements = SQLStatementExtractor::extractFile($schema);
        self::$dataStatements = SQLStatementExtractor::extractFile($data);
    }

    /**
     * Method that loads, parses, and executes the schema files.
     */
    public static function initDb(Connection $conn) {
        self::runStatements(self::$schemaStatements, $conn);
        self::runStatements(self::$dataStatements, $conn);
    }

    /**
     * Executes the passed SQL statements.
     */
    protected static function runStatements($statements, Connection $conn) {
        $stmt = $conn->createStatement();
        foreach($statements as $sql) {
            // print "Executing : $sql \n";
            try {
                $stmt->execute($sql);
            } catch (Exception $e) {
				if (!stripos($e->getMessage(), "drop table")) {
				    print "Error executing SQL: " . $sql . "\n";
                	print $e->getMessage() . "\n";
                	print "=== Attempting to continue === \n";
				}
               
            }
        }
    }

    /**
     * Main worker function.  Adds any available tests to the passed in suite.
     *
     */
    public static function addSuite(PHPUnit2_Framework_TestSuite $parentSuite, $dsn) {

        self::setDSN($dsn);

        // initialize db
        self::init();

        $c = self::$conn;

        // get just the first part of class name (e.g. MySQL from MySQLConnection)
        $camelDriver = str_replace('Connection', '', get_class($c));

        $suite = new PHPUnit2_Framework_TestSuite($camelDriver);

        foreach(self::$driverClasses as $baseClass) {
            // include the test class, based on driver name
            // do we want many?  Let's start by assuming that we'll fit all this in one class.
            $classname = $camelDriver . $baseClass . 'Test';
            $path = 'creole/drivers/' . self::$dsn['phptype'] . '/'. $classname . '.php';
            if (file_exists(CREOLE_TEST_BASE . '/classes/' . $path)) {
                include_once $path;
                if (class_exists($classname)) {
                    $suite->addTestSuite(new ReflectionClass($classname));
                }
            }
        }

        $parentSuite->addTest($suite);

    }


    /**
     * Get an "Exchange" -- which is a query + answer for current RDBMS.
     *
     * @return DBExchange Populated db exchange object.
     */
    public static function getExchange($id) {

        $matches = self::$xpExchanges->query("/exchanges/exchange[@id='".$id."']");

        if (!$matches) {
            print "XPath query matched no nodes: /exchanges/exchange[@id='".$id."']";
            throw new SQLException("XPath query matched no nodes: /exchanges/exchange[@id='".$id."']");
        }

        // otherwise grab first match (there should be only one match)
        $result = is_array($matches) ? $matches[0] : $matches->item(0);

        $exchange = new DBExchange(); // this is the object we send back

        // We know there is a <sql> node
        $sqlNodes = $result->getElementsByTagName("sql");
        // temporary, so we can run this on PHP5b2
        if (is_array($sqlNodes)) $sqlNode = $sqlNodes[0]; else $sqlNode = $sqlNodes->item(0);

        $sql = $sqlNode->nodeValue; // default is to use value of <sql> node.

        // but there may also be a variant for current RDBMS ....
        $variantNodes = $result->getElementsByTagName("sql-variant");
        if ($variantNodes) {
            foreach($variantNodes as $variantNode) {
                if ($variantNode->attributes["id"]->value === self::$dsn['phptype']) {
                    $sql = $variantNode->nodeValue;
                    break;
                }
            }
        }

        $exchange->setSql($sql);

        // now get the result, if any
        $resultNodes = $result->getElementsByTagName("result");
        if ($resultNodes) {
            // temporary, so we can run this on PHP5b2
            $resultNode = is_array($resultNodes) ? $resultNodes[0] : $resultNodes->item(0);
            if ($resultNode) {
                $exchange->setResult($resultNode->nodeValue);
            }
        }

        return $exchange;
    }
}


/**
 * "Inner" class that encapsulates database exchange: query + answer.
 *
 */
class DBExchange {

    private $sql;
    private $res;

    public function setSql($sql) {
        $this->sql = $sql;
    }

    public function getSql() {
        return $this->sql;
    }

    public function setResult($res) {
        $this->res = $res;
    }

    public function getResult() {
        return $this->res;
    }
}