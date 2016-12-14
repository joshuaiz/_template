<?php

abstract class DUPX_DB 
{
	/**   
	 * Connects to a database for usage
	 * @param  string	$host	The host of the database	
	 * @param  string	$user	A valid database user
	 * @param  string	$pass	A valid database password
	 * @param  string	$name	A valid database name
	 * @return void	
	 *
	 * <code>
	 *	    $DB = new DUPX_DB_PDO('mysql');
	 *		$DB->connect('localhost', 'dbuser', 'dbpass', 'dbname');
	 * </code>
	 */
	abstract public function connect($host = 'localhost', $user = '', $pass = '', $name = '', $port = 3306);	

	/**   
	 * Execute a query and return the result
	 * 
	 * @param  string	$sql			A valid sql statement
	 * @param  array	$bind_params	Bind parameters for prepare statement
	 * @return 
	 *	> SELECT, SHOW, DESCRIBE or EXPLAIN 
	 *	Should return a valid result object used to query
	 *	> INSERT
	 * 	Should returns the insert id of the row just inserted
	 *  > UPDATE, DELETE
	 *  Should return the number of rows excepted
	 *
	 * <code>
	 *		$DB->connect('localhost', 'dbuser', 'dbpass', 'dbname');
	 *		$DB->query_opts('SINGLE', 'ASSOC');
	 *		$rows = $DB->query($sql);
	 *		dump($rows);
	 * </code>
	 */
	abstract public function query($sql, $bind_params = null);
	
	/**   
	 * Change the way a query operates and returns data
	 * @param  string	$mode	ALL, SINGLE
	 * @param  string	$style	ASSOC, NUM, BOTH
	 * @return void	
	 *
	 * <code>
	 *		$DB->connect('localhost', 'dbuser', 'dbpass', 'dbname');
	 *		$rows = $DB->query($sql);
	 *		dump($rows);
	 * </code>
	 */
	abstract public function query_opts($mode = 'ALL', $style = 'ASSOC');
	
	abstract public function close();


}


?>
