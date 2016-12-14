<?php
/**   
 * Database utitlites class 
 *
 * <routable> */
class DUPX_DB_MYSQLi extends DUPX_DB 
{
	public $host;
	public $port;
	public $name;
	public $user;
	public $pass;
	public $msyqli_conn;
	
	private $_fetch_mode  = 'ALL';
	private $_fetch_style = MYSQLI_ASSOC;
	
	
	public function connect($host = 'localhost', $user = '', $pass = '', $name = '', $port = 3306) 
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->name = $name;
		$this->msyqli_conn = new mysqli($host, $user, $pass, $name, $port);
		if ($this->msyqli_conn->connect_error) {
			$this->msyqli_conn = null;
			die('Mysqli Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
	}
	
	public function query($sql, $params = null) 
	{
		if ($result = $this->msyqli_conn->query($sql)) 
		{
			//SELECT, SHOW, DESCRIBE or EXPLAIN 
			if (is_object($result)) 
			{
				$data = ($this->_fetch_mode == 'ALL')
						? $result->fetch_all($this->_fetch_style)
						: $result->fetch_array($this->_fetch_style);
				$result->free();
				return $data;				
			} 
			
			//INSERT
			if ($this->msyqli_conn->insert_id != 0) 
			{
				return $this->msyqli_conn->insert_id;
			}
			
			//UPDATE, DELETE
			return $this->msyqli_conn->affected_rows;
			
		} else {
			die($this->msyqli_conn->error);
		}
	}
	
	public function query_opts($mode = 'ALL', $style = 'ASSOC') 
	{
		$this->_fetch_mode = strtoupper($mode);
		
		switch ($style)
		{
			case 'ASSOC': $this->_fetch_style = MYSQLI_ASSOC; 	break;
			case 'NUM'	: $this->_fetch_style = MYSQLI_NUM;		break;
			case 'BOTH' : $this->_fetch_style = MYSQLI_BOTH;	break;
		}
	}
	
	public function close()
	{
		$this->msyqli_conn->close();
	}
	
	public function test_connection()
	{
		
		
	}
}
?>