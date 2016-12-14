<?php
// Exit if accessed directly
if (!defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}

/** * *****************************************************
 *  DUPX_Util
 *  Various Static Utility methods for working with the installer */
class DUPX_Util {
	
	public static $on_php_53_plus;
	
	public static function init()
	{
		self::$on_php_53_plus = version_compare(PHP_VERSION, '5.3.2', '>=');
	}

    /** 
     * Get current microtime as a float. Can be used for simple profiling.
     */
    public static function get_microtime() {
        return microtime(true);
    }

    /** 
     * Return a string with the elapsed time.
     * Order of $end and $start can be switched. 
     */
    public static function elapsed_time($end, $start) {
        return sprintf("%.4f sec.", abs($end - $start));
    }

    /** 
     * @param string $string Thing that needs escaping
     * @param bool $echo   Do we echo or return?
     * @return string    Escaped string. 
     */
    public static function esc_html_attr($string = '', $echo = false) {
        $output = htmlentities($string, ENT_QUOTES, 'UTF-8');
        if ($echo)
            echo $output;
        else
            return $output;
    }

    /** 
     *  Count the tables in a given database
     *  @param string $_POST['dbname']		Database to count tables in 
     */
    public static function dbtable_count($conn, $dbname) {
        $res = mysqli_query($conn, "SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '{$dbname}' ");
        $row = mysqli_fetch_row($res);
        return is_null($row) ? 0 : $row[0];
    }

    /** 
     *  Returns the table count
     *  @param string $conn	        A valid link resource
     *  @param string $table_name	A valid table name
     */
    public static function table_row_count($conn, $table_name) {
        $total = mysqli_query($conn, "SELECT COUNT(*) FROM `$table_name`");
        if ($total) {
            $total = @mysqli_fetch_array($total);
            return $total[0];
        } else {
            return 0;
        }
    }

    /** 
     *  Adds a slash to the end of a path
     *  @param string $path		A path
     */
    public static function add_slash($path) {
        $last_char = substr($path, strlen($path) - 1, 1);
        if ($last_char != '/') {
            $path .= '/';
        }
        return $path;
    }

    /** 
     *  Makes path safe for any OS
     *  Paths should ALWAYS READ be "/"
     * 		uni: /home/path/file.xt
     * 		win:  D:/home/path/file.txt 
     *  @param string $path		The path to make safe
     */
    public static function set_safe_path($path) {
        return str_replace("\\", "/", $path);
    }

	/** 
     *  Undoes a set_safe_path
     *  @param string $path		The path to make unsafe
     */
    public static function unset_safe_path($path) {
        return str_replace("/", "\\", $path);
    }

	/** 
     *  PHP_SAPI for fcgi requires a data flush of at least 256
     *  bytes every 40 seconds or else it forces a script hault
     */
    public static function fcgi_flush() {
        echo(str_repeat(' ', 256));
        @flush();
    }
	
    /** 
     *  A safe method used to copy larger files
     *  @param string $source		The path to the file being copied
     *  @param string $destination	The path to the file being made
     */
    public static function copy_file($source, $destination) {
        $sp = fopen($source, 'r');
        $op = fopen($destination, 'w');

        while (!feof($sp)) {
            $buffer = fread($sp, 512);  // use a buffer of 512 bytes
            fwrite($op, $buffer);
        }
        // close handles
        fclose($op);
        fclose($sp);
    }

    /** 
     *  Finds a string in a file and returns true if found
     *  @param array  $list		A list of strings to search for
     *  @param string $haystack	The string to search in
     */
    public static function string_has_value($list, $file) {
        foreach ($list as $var) {
            if (strstr($file, $var) !== false) {
                return true;
            }
        }
        return false;
    }

    /** 
     *  Returns the active plugins for a package
     *  @param  conn   $dbh	 A database connection handle
     *  @return array  $list A list of active plugins
     */
    public static function get_active_plugins($dbh) {
        $query = @mysqli_query($dbh, "SELECT option_value FROM `{$GLOBALS['FW_TABLEPREFIX']}options` WHERE option_name = 'active_plugins' ");
        if ($query) {
            $row = @mysqli_fetch_array($query);
            $all_plugins = unserialize($row[0]);
            if (is_array($all_plugins)) {
                return $all_plugins;
            }
        }
        return array();
    }

    /** 
     *  Returns the tables for a database
     *  @param  conn   $dbh	 A database connection handle	 
     *  @return array  $list A list of all table names
     */
    public static function get_database_tables($dbh) {
        $query = @mysqli_query($dbh, 'SHOW TABLES');
        if ($query) {
            while ($table = @mysqli_fetch_array($query)) {
                $all_tables[] = $table[0];
            }
            if (isset($all_tables) && is_array($all_tables)) {
                return $all_tables;
            }
        }
        return array();
    }
	
    /**
     * MySQL connection support for sock
     * @param same as mysqli_connect
     * @return database connection handle
     */	
	public static function mysqli_connect( $host, $username, $password, $dbname = '', $port = null ) {
		if ( ! $port ) {
			$port = ini_get("mysqli.default_port");
			$port = empty($port) ? 3306 : $port;
		}

		if ( 'sock' === substr( $host, -4 ) ) {
			$url_parts = parse_url( $host );
			$dbh = @mysqli_connect( 'localhost', $username, $password, $dbname, null, $url_parts['path'] );
		}
		else {
			$dbh = @mysqli_connect( $host, $username, $password, $dbname, $port );
		}
		return $dbh;
	}
	
    /**
     * MySQL database version number
     * @param conn $dbh Database connection handle
     * @return false|string false on failure, version number on success
     */
    public static function mysql_version($dbh) {
        if (function_exists('mysqli_get_server_info')) {
            return preg_replace('/[^0-9.].*/', '', mysqli_get_server_info($dbh));
        } else {
            return 0;
        }
    }

    /**
     * MySQL server variable
     * @param conn $dbh Database connection handle
     * @return string the server variable to query for
     */
    public static function mysql_variable_value($dbh, $variable) {
        $result = @mysqli_query($dbh, "SHOW VARIABLES LIKE '{$variable}'");
        $row = @mysqli_fetch_array($result);
        @mysqli_free_result($result);
        return isset($row[1]) ? $row[1] : null;
    }

    /**
     * Determine if a MySQL database supports a particular feature
     * @param conn $dbh Database connection handle	 
     * @param string $feature the feature to check for
     * @return bool
     */
    public static function mysql_has_ability($dbh, $feature) {
        $version = self::mysql_version($dbh);

        switch (strtolower($feature)) {
            case 'collation' :
            case 'group_concat' :
            case 'subqueries' :
                return version_compare($version, '4.1', '>=');
            case 'set_charset' :
                return version_compare($version, '5.0.7', '>=');
        };
        return false;
    }

    /**
     * Sets the MySQL connection's character set.
     * @param resource $dbh     The resource given by mysqli_connect
     * @param string   $charset The character set (optional)
     * @param string   $collate The collation (optional)
     */
    public static function mysql_set_charset($dbh, $charset = null, $collate = null) {

        $charset = (!isset($charset) ) ? $GLOBALS['DBCHARSET_DEFAULT'] : $charset;
        $collate = (!isset($collate) ) ? $GLOBALS['DBCOLLATE_DEFAULT'] : $collate;

        if (self::mysql_has_ability($dbh, 'collation') && !empty($charset)) {
            if (function_exists('mysqli_set_charset') && self::mysql_has_ability($dbh, 'set_charset')) {
                return mysqli_set_charset($dbh, $charset);
            } else {
                $sql = " SET NAMES {$charset}";
                if (!empty($collate))
                    $sql .= " COLLATE {$collate}";
                return mysqli_query($dbh, $sql);
            }
        }
    }

    /**
     *  Display human readable byte sizes
     *  @param string $size		The size in bytes
     */
    public static function readable_bytesize($size) {
        try {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            for ($i = 0; $size >= 1024 && $i < 4; $i++)
                $size /= 1024;
            return round($size, 2) . $units[$i];
        } catch (Exception $e) {
            return "n/a";
        }
    }
	
	/**
     *  The characters that are special in the replacement value of preg_replace are not the 
	 *  same characters that are special in the pattern
     *  @param string $str		The string to replace on
     */
	public static function preg_replacement_quote($str) {
		return preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', $str);
	}
	
	public static function isJSON($string){
	
		return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}
	
	public static function try_CDN($url, $port)
	{
		if ($GLOBALS['FW_USECDN']) 
		{
			return DUPX_HTTP::is_url_active($url, $port);
		} else {
			return false;
		}
	}
	
	
	public static function dump($var, $pretty = false) {
	if ($pretty) {
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	} else {
		print_r($var);
	}
}

	/**
	*  Does a string have non ascii characters
	*/
	public static function is_non_ascii($string)
    {
		return preg_match('/[^\x20-\x7f]/', $string);
    }


}

DUPX_Util::init();

?>