<?php


require_once 'creole/Connection.php';

/**
 * A good (valid) Connection class that implements croele.Connection
 */
class MyDriverConnection implements Connection {
    
    /**
     * The number of operations in current transaction.
     * @var int
     */ 
    private $transactionOpcount = 0;
    
    /**
     * @var boolean
     */
    private $autocommit = true;    
    
    /**
     * DB connection resource id.     
     * @var resource
     */ 
    private $dblink;        
    
    /**
     * Array hash of connection properties.
     * @var array
     */
    private  $dsn;
    
    /**
     * Any flags (e.g. Creole::PERSISTENT) for current connection.
     * @var int
     */
    private $flags = 0;          
        
    /**
     * @see Connection::connect()
     */
    function connect($dsninfo, $flags = 0)
    {        
       $this->flags = $flags;
       $this->dsn = $dsninfo;
    } 
      
    /**
     * @see Connection::getResource()
     */
    public function getResource() {
        return $this->dblink;
    }
    
    /**
     * @see Connection::getDSN()
     */
    public function getDSN() {
        return $this->dsn;
    }
       
    /**
     * @see Connection::getFlags()
     */
    public function getFlags()
    {
        return $this->flags;
    }    

    /**
     * @see Connection::getAutoCommit()
     */
    public function getAutoCommit()
    {
        return $this->autocommit;
    }
    
    /**
     * @see Connection::setAutoCommit()
     */
    public function setAutoCommit($bit)
    {        
        $this->autocommit = (boolean) $bit;
        if ($bit && $this->transactionOpcount > 0) {
            $this->commit();
        }
    }
    
    /**
     * @see Connection::getDatabaseInfo()
     */
    public function getDatabaseInfo()
    {
        require_once 'creole/drivers/sqlite/metadata/SQLiteDatabaseInfo.php';
        return new SQLiteDatabaseInfo($this);
    }
    
     /**
     * @see Connection::getIdGenerator()
     */
    public function getIdGenerator()
    {
        require_once 'creole/drivers/sqlite/SQLiteIdGenerator.php';
        return new SQLiteIdGenerator($this);
    }
    
    /**
     * @see Connection::prepareStatement()
     */
    public function prepareStatement($sql) 
    {
        $positions = PreparedStatementHelper::getPositions($sql);
        require_once 'creole/drivers/sqlite/SQLitePreparedStatement.php';
        return new SQLitePreparedStatement($this, $sql, $positions);
    }
    
    /**
     * @see Connection::prepareCall()
     */
    public function prepareCall($sql) {
        throw new SQLException('SQLite does not support stored procedures using CallableStatement.');        
    }
    
    /**
     * @see Connection::createStatement()
     */
    public function createStatement()
    {
        require_once 'creole/drivers/sqlite/SQLiteStatement.php';
        return new SQLiteStatement($this);
    }
        
    /**
     * @see Connection::close()
     */
    function close()
    {
        $ret = @sqlite_close($this->dblink);
        $this->dblink = null;
        return $ret;
    }
    
    /**
     * @see Connection::executeQuery()
     */
    public function executeQuery($sql, $fetchmode = null)
    {    
        $result = @sqlite_query($this->dblink, $sql);
        if (!$result) {
            throw new SQLException('Could not execute query', $php_errormsg, $sql); //sqlite_error_string(sqlite_last_error($this->dblink))
        }
        require_once 'creole/drivers/sqlite/SQLiteResultSet.php';
        return new SQLiteResultSet($this, $result, $fetchmode);    
    }    
    
    /**
     * @see Connection::executeUpdate()
     */
    function executeUpdate($sql)
    {
        if (!$this->autocommit) {
            if ($this->transactionOpcount === 0) {
                $result = @sqlite_query($this->dblink, 'BEGIN');
                if (!$result) {
                    throw new SQLException('Could not begin transaction', $php_errormsg); //sqlite_error_string(sqlite_last_error($this->dblink))
                }
            }
            $this->transactionOpcount++;
        }
        
        $result = @sqlite_query($this->dblink, $sql);
        if (!$result) {            
            throw new SQLException('Could not execute update', $php_errormsg); //sqlite_error_string(sqlite_last_error($this->dblink))
        }
        return (int) @sqlite_changes($this->dblink);
    }

    function begin()
    {
    }
        
    /**
     * Commit the current transaction.
     */
    function commit()
    {
        if ($this->transactionOpcount > 0) {            
            $result = @sqlite_query($this->dblink, 'COMMIT');
            $this->transactionOpcount = 0;
            if (!$result) {
                throw new SQLException('Can not commit transaction', $php_errormsg); // sqlite_error_string(sqlite_last_error($this->dblink))
            }
        }
    }

    /**
     * Roll back (undo) the current transaction.
     * @throws SQLException
     * @return void
     */
    function rollback()
    {
        if ($this->transactionOpcount > 0) {            
            $result = @sqlite_query($this->dblink, 'ROLLBACK');
            $this->transactionOpcount = 0;
            if (!$result) {
                throw new SQLException('Could not rollback transaction', $php_errormsg); // sqlite_error_string(sqlite_last_error($this->dblink))
            }
        }
    }

    /**
     * Gets the number of rows affected by the data manipulation
     * query.
     *
     * @return int Number of rows affected by the last query.
     */
    function getUpdateCount()
    {
        return (int) @sqlite_changes($this->dblink);
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
        $sql .= " LIMIT ".$limit." OFFSET ".$offset;
    }

	/**
	 * Returns false if connection is closed.
	 * @return boolean
	 */
	public function isConnected()
	{
		return !empty($this->dblink);
	}

}
