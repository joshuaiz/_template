<?php

/**   
 * Database utitlites class 
 *
 * <routable> */
class DUPX_DB_PDO extends DUPX_DB 
{
	public $driver;
	public $host;
	public $port;
	public $name;
	public $user;
	public $pass;
	public $pdo_conn;
	
	private $_fetch_mode  = 'ALL';
	private $_fetch_style = PDO::FETCH_ASSOC;
	
	
	public function __construct($driver='mysql') 
	{
		$this->driver = $driver;
	}
	
	public function connect($host = 'localhost', $user = '', $pass = '', $name = '', $port = 3306) 
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->name = $name;
		
		try {
			$this->pdo_conn = new PDO("{$this->driver}:host={$host};dbname={$name}", $user, $pass);
			
		} catch (PDOException $e) {
			$this->pdo_conn = null;
			die("PDO Connection Error!: " . $e->getMessage());
		}

	}
	
	public function query($sql, $bind_params = null) 
	{
		if ($result = $this->pdo_conn->query($sql)) 
		{
			if (is_object($result)) 
			{
				if ($result->columnCount() != 0) 
				{
					//SELECT, SHOW, DESCRIBE or EXPLAIN 
					return ($this->_fetch_mode == 'ALL')
							? $result->fetchAll($this->_fetch_style)
							: $result->fetch($this->_fetch_style);
				} 
				else 
				{
					//INSERT
					$insert_id = $this->pdo_conn->lastInsertId();
					if ($insert_id != 0 )
						return $insert_id;
					
					//UPDATE, DELETE
					return $result->rowCount();
				}
			} 
			return false;
		} 
		else 
		{
			die($this->pdo_conn->error);
		}
	}
	
	public function query_opts($mode = 'ALL', $style = 'ASSOC') 
	{
		$this->_fetch_mode = $mode;
		switch ($style)
		{
			case 'ASSOC': $this->_fetch_style = PDO::FETCH_ASSOC;	break;
			case 'NUM'	: $this->_fetch_style = PDO::FETCH_NUM;		break;
			case 'BOTH' : $this->_fetch_style = PDO::FETCH_BOTH;	break;
		}
	}
	
	public function close()
	{
		$this->pdo_conn->close();
	}
	
}

?>