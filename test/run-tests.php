<?php

    define('CREOLE_TEST_BASE', dirname(__FILE__));    
    define('PHPUnit2_MAIN_METHOD', "don't let PHPUnit try to auto-invoke anything!");
    ini_set('include_path',  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . PATH_SEPARATOR . ini_get('include_path'));

    /*
    ini_set('include_path',  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . 
                            PATH_SEPARATOR . CREOLE_TEST_BASE .  DIRECTORY_SEPARATOR . '..'  . DIRECTORY_SEPARATOR . 'classes' . 
                            PATH_SEPARATOR . ini_get('include_path'));
    */

    include_once 'PHPUnit2/TextUI/TestRunner.php';
    include_once 'PHPUnit2/Framework/TestSuite.php';
    include_once 'Benchmark/Timer.php';

    if (!class_exists('PHPUnit2_Framework_TestSuite')) {
        die("You must have PHPUnit2 >= 2.0.0 installed.\nSee http://pear.php.net/package/PHPUnit2 for details.");
    }
    
    if (!isset($argv)) {
        $argc = $_SERVER['argc'];
        $argv = $_SERVER['argv'];        
    }
    
    require_once 'creole/CreoleBaseTest.php';
    set_error_handler(array('CreoleBaseTest', 'errorHandler'));  
    
    
    // if no DSN was supplied, then we need to prompt for it
    if ($argc <= 1) {
        print "\n";
        print "You must provide connection information to a database in order to test\n";
        print "driver classes.  The database should be temporary or disposable.  The \n";
        print "unit tests will create and drop tables within the specified database.\n";
        print "\nYou can also specify this on the commmandline:\n";
        print "\t$> php -f run-tests.php mysql://root@localhost/mytempdb\n";
        
        print "\nPlease enter the DSN URL (e.g. sqlite://localhost/:memory:)\n";
        print "DSN: ";
        $dsn = trim(fgets(STDIN));
    } else {
        $dsn = $argv[1];
    }

    $testSuite = new PHPUnit2_Framework_TestSuite($dsn);
        
    // ----------------------------------------------------------------------------
    // TESTS ----------------------------------------------------------------------
    // ----------------------------------------------------------------------------
    
    $timer = new Benchmark_Timer();    
    $timer->start();  
    
    // (1) Add Generic (non-Driver) Tests
    // ----------------------------------
            
    require_once 'creole/CreoleTest.php';
    $testSuite->addTestSuite(new ReflectionClass('CreoleTest'));                 

	require_once 'creole/util/sql/SQLStatementExtractorTest.php';
	$testSuite->addTestSuite(new ReflectionClass('SQLStatementExtractorTest'));

    // (2) Driver Tests
    // ----------------
        
    include_once 'creole/drivers/DriverTestManager.php';
        
    print "--------------------------------------\n";
    print "| Running driver tests               |\n";
    print "--------------------------------------\n";        
        
    $timer->setMarker("start driver tests");
    
    print "DSN: " . $dsn . "\n\n";
    
    print "[It is safe to ignore any errors related to dropping nonexistant tables.]\n\n";
    
    try {
        DriverTestManager::addSuite($testSuite, $dsn);
        PHPUnit2_TextUI_TestRunner::run($testSuite);
    } catch(Exception $e) {
        print "Could not add suite for " . $dsn . ": " . $e->getMessage();
    }
    
    $timer->stop();    
    
    $timer->display();

?>
