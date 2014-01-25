<?php
////////////////////////////////////////////////////////////////////////
//	Database
////////////////////////////////////////////////////////////////////////
/**
*	Database interface
*/
interface iDatabase {
	public static function getInstance(); //get the instance of Database
	public function query($query); //run the query
	public function lastInsertId(); //get the last inserted ID
	public function beginTransaction(); //begin a transaction ( you need InnoDB for this ), 
	//read more about transitions here http://www.sitepoint.com/mysql-transactions-php-emulation/
	public function commit(); // commit to database ( you need InnoDB for this )
	public function rollBack(); // rollback changes ( you need InnoDB for this )
	// private function error();	- 	hadles your catched errors (placeholder for later code)
}
/**
* Database - singelton pattern databas class
*
* @author laxxen
* @example $_db = Database::getInstance();
* @see query("selet * from users where id=?", $ID);
*/
class Database implements iDatabase
{	
	/**
	* Private variables to connect to the database
	* @access private
	*/
    private $SQL_USR = "root"; //username to sql server
    private $SQL_PWD = "VeryS3cretAndSn3akyPassw0rd!!!"; //password
    private $SQL_SERVER = "localhost"; //server
    private $SQL_DB = "MyPresious"; //database
	
	private $_db; // PDO connection
	private static $_instance; //the instance
	
	public $xss_prev = true; //condition for xss preventation
	
	/**
	*	Standard __construct.
	*	starting a pdo connection with utf charset (same as mysql server) and
	*	sets emurate attributes toi false to prevent outofcharset sql injections
	*	@access private
	*/
	private function __construct() // private so only 1 connection at a time can be made
	{
		try {
			$this->_db = new PDO("mysql:host=$this->SQL_SERVER;dbname=$this->SQL_DB;charset=utf8", $this->SQL_USR, $this->SQL_PWD); //PDO connection
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //set error mode
			$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // see below
			//source for charset security issue http://stackoverflow.com/a/12202218 
		} catch (PDOException $e) {
			$this->error($e->getMessage()); // here you can call to a own error function if you have
		}
	}
	/**
	*	clone preventation (singelton pattern)
	*	@access private
	*/
	private function __clone() {} 
	
	/**
	*	getInstance	-	so you can get an instance
	*	@access public
	*	@static
	*	@return mixed - returns self
	*/
	public static function getInstance()
	{
		if (!(self::$_instance instanceof self)) //check if we don't alread have an instance of self
			self::$_instance = new self(); //if not create one, the only one
		return self::$_instance; //return self
	}
	/**
	*	query()
	*
	*	Fast secure query with xss and sql-injection preventation
	*
	*	@access public
	*	@argument bool $xss_rev automaticly sets to true after each run. true = xss preventation, false = xss vurlerable
	*	@param string $query the query
	*	@param mixed $other other parametrar
	*	@return mixed - returns the results
	*
	*	@example query("SELECT * FROM users WHERE ID=?", 1);
	*/
	public function query($query)//own prepered query
	{
		try {
			$res = $this->_db->prepare($query); //starting prepared statement
			if (!$res)
				throw new PDOException("wrong with query...");
				
			$args = func_get_args();  //Get all arguments
			array_shift($args);//hides the $query argument
			if (count($args) >= 1) // one or more?
			{  
				for ($i = 0; $i<count($args); $i++) //loop all arguments
					$args[$i] = ($this->xss_prev)? htmlspecialchars($args[$i]) : $args[$i]; //xss preventation 
			}
			$res->execute($args);//exec the query later, works both with and without extra arguments
			$this->xss_prev = true; //reset xss preventation to true for security reasons
			return $res; //returns results
			
		} catch (PDOException $e) {
			$this->error($e->getMessage()); // here you can call to a own error function if you have
		}
	}
	
	/**
	*	lastInsertId - PDO function to get last id
	*	
	*	@access public
	*/
	public function lastInsertId()
	{
		return $this->_db->lastInsertId(); //return the last inserted id
	}
	
	/**
	*	beginTransaction()	-	begin a transaction ( you need InnoDB for this )
	*/
	public function beginTransaction()
	{
		return $this->_db->beginTransaction(); //use pdo function
	}
	
	/**
	*	commit()	-	commit to database ( you need InnoDB for this )
	*/
	public function commit()
	{
		return $this->_db->commit(); //use pdo function
	}
	
	/**
	*	rollback()	-	rollback changes ( you need InnoDB for this )
	*/
	public function rollBack()
	{
		return $this->_db->rollBack(); //use pdo function
	}
	
	/**
	*	error()		-	error function, placeholder for later
	*	@access private
	*	@param string $msg	-	error message
	*/
	private function error($msg)
	{
		die($msg); //die on error
	}
}
////////////////////////////////////////////////////////////////////////
//	Database ENDS
////////////////////////////////////////////////////////////////////////

?>
