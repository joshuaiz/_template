<?php
/*
  Duplicator Pro Plugin
  Copyright (C) 2016, Snap Creek LLC
  website: snapcreek.com

  Duplicator Pro Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once('class.json.entity.base.php');

if (!class_exists('DUP_PRO_Package_Template_Entity'))
{
    /**
     * @copyright 2016 Snap Creek LLC
     */
    class DUP_PRO_Package_Template_Entity extends DUP_PRO_JSON_Entity_Base
    {
        public $name = '';        
        public $notes;
                
        //ARCHIVE:Files
        public $archive_filter_on = 0;      // Enable File Filters
        public $archive_filter_dirs = '';   // Filtered Directories
        public $archive_filter_exts = '';   // Filtered Extensions
        public $archive_filter_files = '';  // Filtered Files		
	       
        //ARCHIVE:Database
        public $database_filter_on = 0;					// Enable Table Filters
        public $database_filter_tables = '';			// List of filtered tables               
		public $database_compatibility_modes = array();	// Older style sql compatibility
        
        //INSTALLER
		//Setup
		public $installer_opts_secure_on;		// Enable Password Protection
		public $installer_opts_secure_pass;		// Password Protection password
		public $installer_opts_skip_scan;		// Skip Scanner
		//Basic DB
        public $installer_opts_db_host;			// MySQL Server Host
        public $installer_opts_db_name;			// Database
        public $installer_opts_db_user;			// User
		//cPanel Login
		public $installer_opts_cpnl_enable = false;
		public $installer_opts_cpnl_host = '';
		public $installer_opts_cpnl_user = '';	
		public $installer_opts_cpnl_pass = '';
		//cPanel DB
		public $installer_opts_cpnl_db_action = 'create';
		public $installer_opts_cpnl_db_host = '';
		public $installer_opts_cpnl_db_name = '';
		public $installer_opts_cpnl_db_user = '';
		
		//Adv Opts
        public $installer_opts_ssl_admin;		// Enforce SSL on Admin
        public $installer_opts_ssl_login;		// Enforce SSL for Logins
        public $installer_opts_cache_wp;		// K??
        public $installer_opts_cache_path;		// ??
        public $installer_opts_url_new;			// New URL
        
        public $is_default = false;
		public $is_manual = false;

        function __construct()
        {
            parent::__construct();
                                
            $this->verifiers['name'] = new DUP_PRO_Required_Verifier('Name must not be blank');     
            $this->name = DUP_PRO_U::__('New Template');
        }
        
        public static function create_default()
        {
            if(self::get_default_template() == null)
            {
                $template = new DUP_PRO_Package_Template_Entity();

                $template->name = DUP_PRO_U::__('Default');
                $template->notes = DUP_PRO_U::__('The default template.');
                $template->is_default = true;
                                
                $template->save();
                DUP_PRO_U::log('Created default template');
            }
            else
            {
                // Update it
                DUP_PRO_U::log('Default template already exists so not creating');    
            }                    
        }
		
		public static function create_manual()
        {
            if(self::get_manual_template() == null)
            {
                $template = new DUP_PRO_Package_Template_Entity();

                $template->name = DUP_PRO_U::__('[Manual Mode]');
                $template->notes = '';
                $template->is_manual = true;
				
				// Copy over the old temporary template settings into this
				$temp_package = DUP_PRO_Package::get_temporary_package(); 
				
				if($temp_package != null)
				{						
					$template->archive_filter_on = $temp_package->Archive->FilterOn;
					$template->archive_filter_dirs = $temp_package->Archive->FilterDirs;
					$template->archive_filter_exts = $temp_package->Archive->FilterExts;
					$template->archive_filter_files = $temp_package->Archive->FilterFiles;
					
					$template->database_filter_on = $temp_package->Database->FilterOn;
					$template->database_filter_tables = $temp_package->Database->FilterTables;
					$template->database_compatibility_modes = $temp_package->Database->Compatible;
					
					$template->installer_opts_cache_path = $temp_package->Installer->OptsCachePath;
					$template->installer_opts_cache_wp = $temp_package->Installer->OptsCacheWP;
					$template->installer_opts_db_host = $temp_package->Installer->OptsDBHost;
					$template->installer_opts_db_name = $temp_package->Installer->OptsDBName;
					$template->installer_opts_db_user = $temp_package->Installer->OptsDBUser;
					$template->installer_opts_secure_on = $temp_package->Installer->OptsSecureOn;
					$template->installer_opts_secure_pass = $temp_package->Installer->OptsSecurePass;
					$template->installer_opts_skip_scan = $temp_package->Installer->OptsSkipScan;
					$template->installer_opts_ssl_admin = $temp_package->Installer->OptsSSLAdmin;
					$template->installer_opts_ssl_login = $temp_package->Installer->OptsSSLLogin;
					$template->installer_opts_url_new = $temp_package->Installer->OptsURLNew;
					
					/* @var $global DUP_PRO_Global_Entity */
					$global = DUP_PRO_Global_Entity::get_instance();
					$global->manual_mode_storage_ids = array();
					
					foreach($temp_package->get_storages() as $storage)
					{
						/* @var $storage DUP_PRO_Storage_Entity */
						array_push($global->manual_mode_storage_ids, $storage->id);
					}	
					
					$global->save();
				}
                                
                $template->save();
                DUP_PRO_U::log('Created manual mode template');
            }
            else
            {
                // Update it
                DUP_PRO_U::log('Manual mode template already exists so not creating');    
            }                    
        }
        
        public function set_post_variables($post)
        {   
			//Archive
            $this->set_checkbox_variable($post, '_archive_filter_on', 'archive_filter_on');
			$this->set_checkbox_variable($post, '_database_filter_on', 'database_filter_on');           
			
			//Installer
			$this->set_checkbox_variable($post, '_installer_opts_secure_on', 'installer_opts_secure_on');   
			$this->set_checkbox_variable($post, '_installer_opts_skip_scan', 'installer_opts_skip_scan');   
			$this->set_checkbox_variable($post, '_installer_opts_cpnl_enable', 'installer_opts_cpnl_enable');  
            $this->set_checkbox_variable($post, '_installer_opts_ssl_admin', 'installer_opts_ssl_admin');            
            $this->set_checkbox_variable($post, '_installer_opts_ssl_login', 'installer_opts_ssl_login');
            $this->set_checkbox_variable($post, '_installer_opts_cache_wp', 'installer_opts_cache_wp');
            $this->set_checkbox_variable($post, '_installer_opts_cache_path', 'installer_opts_cache_path');         
			$this->set_checkbox_variable($post, '_installer_opts_url_new', 'installer_opts_url_new');
			
			$this->installer_opts_secure_pass = base64_encode($post['_installer_opts_secure_pass']);
			
            parent::set_post_variables($post);
			
        }
        
        private function set_checkbox_variable($post, $key, $name)
        {
            if(isset($post[$key]))
            {
                $this->$name = 1;
            }
            else
            {
                $this->$name = 0;
            }
        }  
        
        public function copy_from_source_id($source_template_id)
        {
            $source_template = self::get_by_id($source_template_id);
                        
            $this->archive_filter_dirs = $source_template->archive_filter_dirs;
            $this->archive_filter_exts = $source_template->archive_filter_exts;
            $this->archive_filter_files = $source_template->archive_filter_files;
            $this->archive_filter_on = $source_template->archive_filter_on;			
			
            $this->database_filter_on = $source_template->database_filter_on;
            $this->database_filter_tables = $source_template->database_filter_tables;
			$this->database_compatibility_modes = $source_template->database_compatibility_modes;
			
            $this->installer_opts_cache_path = $source_template->installer_opts_cache_path;
            $this->installer_opts_cache_wp = $source_template->installer_opts_cache_wp;
            $this->installer_opts_db_host = $source_template->installer_opts_db_host;
            $this->installer_opts_db_name = $source_template->installer_opts_db_name;
            $this->installer_opts_db_user = $source_template->installer_opts_db_user;
            $this->installer_opts_ssl_admin = $source_template->installer_opts_ssl_admin;
            $this->installer_opts_ssl_login = $source_template->installer_opts_ssl_login;
            $this->installer_opts_url_new = $source_template->installer_opts_url_new;            
			
			//CPANEL
			$this->installer_opts_cpnl_host = $source_template->installer_opts_cpnl_host;
			$this->installer_opts_cpnl_user = $source_template->installer_opts_cpnl_user;
			$this->installer_opts_cpnl_pass = $source_template->installer_opts_cpnl_pass;
			$this->installer_opts_cpnl_enable = $source_template->installer_opts_cpnl_enable;

			//CPANEL DB
			//1 = Create New, 2 = Connect Remove
			$this->installer_opts_cpnl_db_action = $source_template->installer_opts_cpnl_db_action;
			$this->installer_opts_cpnl_db_host = $source_template->installer_opts_cpnl_db_host;
			$this->installer_opts_cpnl_db_name = $source_template->installer_opts_cpnl_db_name;
			$this->installer_opts_cpnl_db_user = $source_template->installer_opts_cpnl_db_user;
		
            $this->name = sprintf(DUP_PRO_U::__('%1$s - Copy'), $source_template->name);
			
            $this->notes = $source_template->notes;
        }
        
        public static function populate_from_post($post)
        {
            $filter_exts	= isset($post['filter-exts']) ? $this->parseExtensionFilter($post['filter-exts']) : '';
            $filter_dirs	= isset($post['filter-dirs']) ? $this->parseDirectoryFilter($post['filter-dirs']) : '';
            $filter_files	= isset($post['filter-files']) ? $this->parseExtensionFilter($post['filter-files']) : '';
            $tablelist		= isset($post['dbtables']) ? implode(',', $post['dbtables']) : '';
			$compatlist		= isset($post['dbcompat']) ? implode(',', $post['dbcompat']) : '';
            
            // Archive
            $this->archive_filter_on = isset($post['filter-on']) ? 1 : 0;
            $this->archive_filter_dirs = esc_html($filter_dirs);
            $this->archive_filter_exts = str_replace(array('.', ' '), "", esc_html($filter_exts));
            $this->archive_filter_files = str_replace(array('.', ' '), "", esc_html($filter_files));			
            
            // Installer
            $this->installer_opts_db_host = esc_html($post['dbhost']);
            $this->installer_opts_db_name = esc_html($post['dbname']);
            $this->installer_opts_db_user = esc_html($post['dbuser']);
            $this->installer_opts_ssl_admin = isset($post['ssl-admin']) ? 1 : 0;
            $this->installer_opts_ssl_login = isset($post['ssl-login']) ? 1 : 0;
            $this->installer_opts_cache_wp = isset($post['cache-wp']) ? 1 : 0;
            $this->installer_opts_cache_path = isset($post['cache-path']) ? 1 : 0;
            $this->installer_opts_url_new = esc_html($post['url-new']);
			//CPANEL
			$this->installer_opts_cpnl_host = esc_html($post['installer_opts_cpnl_host']);
			$this->installer_opts_cpnl_user = esc_html($post['installer_opts_cpnl_user']);
			$this->installer_opts_cpnl_pass = esc_html($post['installer_opts_cpnl_pass']);
			//$this->installer_opts_cpnl_enable = $source_template->installer_opts_cpnl_enable;

			//CPANEL DB
			//1 = Create New, 2 = Connect Remove
			$this->installer_opts_cpnl_db_action = esc_html($post['installer_opts_cpnl_db_action']);
			$this->installer_opts_cpnl_db_host = esc_html($post['installer_opts_cpnl_db_host']);
			$this->installer_opts_cpnl_db_name = esc_html($post['installer_opts_cpnl_db_name']);
			$this->installer_opts_cpnl_db_user = esc_html($post['installer_opts_cpnl_db_user']);
            
            // Database
            $this->database_filter_on = isset($post['dbfilter-on']) ? 1 : 0;
            $this->database_filter_tables = esc_html($tablelist);
			$this->database_compatibility_modes = $compatlist;
        }
        
        public static function compare_templates($a, $b)
        {
            /* @var $a DUP_PRO_Package_Template_Entity */
            /* @var $b DUP_PRO_Package_Template_Entity */
            
            if($a->is_default)
            {
                return -1;
            }
            else if($b->is_default)
            {
                return 1;
            }
            else
            {
                return strcasecmp($a->name, $b->name);
            }            
        }
        
        public static function get_all($include_manual_mode = false)
        {           
            $templates =  self::get_by_type(get_class());
            			
			if($include_manual_mode === false)
			{
				$filtered_templates = array();
				
				foreach($templates as $template)
				{
					/* @var $template DUP_PRO_Package_Template_Entity */
					if($template->is_manual === false)
					{
						array_push($filtered_templates, $template);
					}
				}
			}
			else
			{
				$filtered_templates = $templates;
			}
			
            usort($filtered_templates, array('DUP_PRO_Package_Template_Entity', 'compare_templates'));
            
            return $filtered_templates;
        }    
        
        public static function delete_by_id($template_id)
        {    
            $schedules = DUP_PRO_Schedule_Entity::get_by_template_id($template_id);
            
            foreach($schedules as $schedule)
            {                
                /* @var $schedule DUP_PRO_Schedule_Entity */
                $schedule->template_id = self::get_default_template()->id;
                
                $schedule->save();
            }
            
            parent::delete_by_id_base($template_id);
        }     
        
        public static function get_default_template()
        {
            $templates = self::get_all();
            
            foreach($templates as $template)
            {
                /* @var $template DUP_PRO_Package_Template_Entity */
                if($template->is_default)
                {
                    return $template;
                }
            }

            return null;
        }
		
		public static function get_manual_template()
        {
            $templates = self::get_all(true);
            
            foreach($templates as $template)
            {
                /* @var $template DUP_PRO_Package_Template_Entity */
                if($template->is_manual)
                {
                    return $template;
                }
            }

            return null;
        }

        /**
         * 
         * @param type $id
         * @return DUP_PRO_Package_Template_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class());
        }
    }           
}
?>