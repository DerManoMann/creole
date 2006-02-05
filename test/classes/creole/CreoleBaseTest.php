<?php

require_once 'PHPUnit2/Framework/TestCase.php';

/**
 * Base class for Creole unit tests.
 *
 * @version $Id: CreoleBaseTest.php,v 1.3 2004/06/09 01:22:46 hlellelid Exp $
 * @copyright 2003 
 **/
class CreoleBaseTest extends PHPUnit2_Framework_TestCase {

    private static $warnings = array();
    
    /**
     * Assert that an exception is expected to match string
     */
    protected function expectException($excstr, $e) {
        if (stripos($e->getMessage(), $excstr) !== false) {
                $found = true;
        } else {
            $this->fail("Did not find expected exception containing string: " . $excstr . "(Actual: " . $e->getMessage() . ")");
        }        
    }
    
    /**
     * Assert that a warning is expected to be present.
     * This is referring to E_USER_WARNING messages raised using trigger_error().
     */
    protected function expectWarning($warnstr) {
        $found = false;
        foreach(self::$warnings as $warning) {
            if (stripos($warning, $warnstr) !== false) {
                $found = true;
            }
        }
        if (!$found) {
            $this->fail("Did not find expected warning containing string: " . $warnstr);
        }
        $this->clearWarnings();
    }
    
    /**
     * Reset the warnings array.
     */
    protected function clearWarnings() {
        self::$warnings = array();
    }
    
    /**
     * Handles PHP native errors, pushes E_USER errors or warnings onto a stack
     * so that they can be searched for with expectWarning(), etc. methods.
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $rptlevel = error_reporting();
        if ($rptlevel > 0) {
              switch ($errno) {
                  case E_USER_WARNING:
                    self::$warnings[] = $errstr;
                  break;
                  case E_USER_ERROR:
                   echo "\nERROR! [$errno] $errstr on line $errline of $errfile\n";
                   break;                  
                  case E_WARNING:
                   echo "\nWARNING! [$errno] $errstr on line $errline of $errfile\n";
                   break;
                  case E_USER_NOTICE:
                  case E_NOTICE:
                   echo "\nNOTICE! [$errno] $errstr on line $errline of $errfile\n";
                   break;
                  default:
                   echo "\nUnkown error type: [$errno] $errstr on line $errline of $errfile\n";
                   break;
              }
        }
    }


}
