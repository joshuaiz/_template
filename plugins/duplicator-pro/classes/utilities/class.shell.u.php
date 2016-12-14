<?php
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/entities/class.global.entity.php');

if (!class_exists('DUP_PRO_Shell_U'))
{
	class DUP_PRO_Shell_U 
	{
		public static function execute_and_get_value($command, $index)
        {
            $command = "$command | awk '{print $$index }'";
            
            $ret_val = shell_exec($command);                        
            
            return trim($ret_val);
        }
		
		
				/**
		 * Escape a string to be used as a shell argument with bypass support for Windows
		 * 
		 *	NOTES: 
		 *		Provides a way to support shell args on Windows OS and allows %,! on Windows command line
		 *		Safe if input is know such as a defined constant and not from user input escapeshellarg
		 *		on Windows with turn %,! into spaces
		 * 
		 * @return string
		 */
		public static function escapeshellarg_winsupport($string)
        {
            if (strncasecmp(PHP_OS, 'WIN', 3) == 0) 
			{
				if (strstr($string, '%') || strstr($string, '!')) 
				{
					$result = '"' . str_replace('"', '', $string) . '"';
					return $result;
				}
			} 
			return escapeshellarg($string);
        }
		
		public static function get_compression_parameter()
		{
			/* @var $global DUP_PRO_Global_Entity */
			$global = DUP_PRO_Global_Entity::get_instance();
			
			if($global->archive_compression)
			{
				$parameter = '-6';
			}
			else
			{
				$parameter = '-0';
			}
			
			return $parameter;
		}				
	}
	
	
}
?>
