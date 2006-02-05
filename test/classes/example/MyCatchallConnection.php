<?php


require_once 'creole/Connection.php';

/**
 * A "catchall" Connection class that implements a decorator pattern
 * to "wrap" any native RDBMS driver.
 */
class MyCatchallConnection implements Connection {
       
    public $driver;
         
    /**
     * @see Connection::connect()
     */
    function connect($dsninfo, $flags = 0)
    {        
        $class = Creole::getDriver($dsninfo['phptype']);
        $class = Creole::import($class);
        
        $this->driver = new $class();
    } 
      
    /**
     * @see Connection::getResource()
     */
    public function getResource() {
        return $this->driver->getResource();
    }
    
    /**
     * @see Connection::getDSN()
     */
    public function getDSN() {
        return $this->driver->getDSN();
    }
       
    /**
     * @see Connection::getFlags()
     */
    public function getFlags()
    {
        return $this->driver->getFlags();
    }    

    /**
     * @see Connection::getAutoCommit()
     */
    public function getAutoCommit()
    {
        return $this->driver->getAutoCommit();
    }
    
    /**
     * @see Connection::setAutoCommit()
     */
    public function setAutoCommit($bit)
    {        
        $this->driver->setAutoCommit($bit);
    }
    
    /**
     * @see Connection::getDatabaseInfo()
     */
    public function getDatabaseInfo()
    {
        return $this->driver->getDatabaseInfo();
    }
    
     /**
     * @see Connection::getIdGenerator()
     */
    public function getIdGenerator()
    {
        return $this->driver->getIdGenerator();
    }
    
    /**
     * @see Connection::prepareStatement()
     */
    public function prepareStatement($sql) 
    {
        return $this->driver->prepareStatement($sql);
    }
    
    /**
     * @see Connection::prepareCall()
     */
    public function prepareCall($sql) {
        return $this->driver->prepareCall($sql);
    }
    
    /**
     * @see Connection::createStatement()
     */
    public function createStatement()
    {
        return $this->driver->createStatement();
    }
        
    /**
     * @see Connection::close()
     */
    function close()
    {
        return $this->driver->close();
    }
    
    /**
     * @see Connection::executeQuery()
     */
    public function executeQuery($sql, $fetchmode = null)
    {    
        return $this->driver->executeQuery($sql, $fetchmode);
    }    
    
    /**
     * @see Connection::executeUpdate()
     */
    function executeUpdate($sql)
    {
        $this->driver->executeUpdate($sql);
    }

    function begin()
    {
    }
    
    /**
     * Commit the current transaction.
     */
    function commit()
    {
        return $this->driver->commit();
    }

    /**
     * Roll back (undo) the current transaction.
     * @throws SQLException
     * @return void
     */
    function rollback()
    {
        return $this->driver->rollback();
    }

    /**
     * Gets the number of rows affected by the data manipulation
     * query.
     *
     * @return int Number of rows affected by the last query.
     */
    function getUpdateCount()
    {
        return $this->driver->getUpdateCount();
    }


    /**
     * If RDBMS supports native LIMIT/OFFSET then query SQL is modified
     * so that no emulation is performed in ResultSet.
     * 
     * By default this method adds LIMIT/OFFSET in the style 
     * " LIMIT $limit OFFSET $offset"  to end of SQL.
     * 
     * @param string &$sql The query that will be modified.
     * @param int $offset
     * @param int $limit
     * @return boolean Whether the query was modified.
     * @throws SQLException - if unable to modify query for any reason.
     */
    public function applyLimit(&$sql, $offset, $limit)
    {
        return $this->driver->applyLimit($sql, $offset, $limit);
    }

	/**
	 * Returns false if connection is closed.
	 * @return boolean
	 */
	public function isConnected()
	{
		return $this->driver->isConnected();
	}

}
