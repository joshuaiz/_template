<?php //

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

require_once(dirname(__FILE__) . '/class.json.entity.base.php');
require_once(dirname(__FILE__) . '/../utilities/class.u.php');


if (!class_exists('DUP_PRO_Recommended_Fix_Type'))
{

    /**
     * @copyright 2016 Snap Creek LLC
	 * Add to this class if/when we have more complex fix logic
     */
    abstract class DUP_PRO_Recommended_Fix_Type
    {
        const Text = 0;
    }
}

if (!class_exists('DUP_PRO_Recommended_Fix'))
{

    /**
     * @copyright 2016 Snap Creek LLC
	 * recommendation_type = Text; parameter1 = {text to display to user}; parameter2=n/a
     */
    class DUP_PRO_Recommended_Fix
    {		
        public $recommended_fix_type;
		public $error_text = '';
		public $parameter1 = '';
		public $parameter2 = '';	
    }
}

if (!class_exists('DUP_PRO_System_Global_Entity'))
{
    /**
     * @copyright 2016 Snap Creek LLC
     */
    class DUP_PRO_System_Global_Entity extends DUP_PRO_JSON_Entity_Base
    {	
		const NAME_IN_GLOBALS = 'dup_pro_system_global';
		public $recommended_fixes;
	
        public static function initialize_plugin_data()
        {
            $system_globals = parent::get_by_type(get_class());
            if (count($system_globals) == 0)
            {
                $system_global = new DUP_PRO_System_Global_Entity();
				$system_global->recommended_fixes = array();
                $system_global->save();
            }
        }
		
		public function add_recommended_text_fix($error_text, $fix_text)
		{
			if($this->is_text_fix_dupe($fix_text) === false)
			{
				$fix = new DUP_PRO_Recommended_Fix();

				$fix->recommended_fix_type = DUP_PRO_Recommended_Fix_Type::Text;
				$fix->error_text = $error_text;
				$fix->parameter1 = $fix_text;

				array_push($this->recommended_fixes, $fix);
			}
		}
		
		private function is_text_fix_dupe($fix_text)
		{
			$existing_strings = $this->get_recommended_text_fix_strings();

			$present = false;

			foreach($existing_strings as $existing_string)
			{
				if(strcmp($existing_string, $fix_text) == 0)
				{
					$present = true;
					break;
				}
			}
				
			return $present;
		}
		
		public function clear_recommended_fixes()
		{
			unset($this->recommended_fixes);
			
			$this->recommended_fixes = array();
		}
		
		public function get_recommended_text_fix_strings()
		{
			$text_fix_strings = array();
			
			/* @var $$fix DUP_PRO_Recommended_Fix */
			foreach($this->recommended_fixes as $fix)
			{
				if($fix->recommended_fix_type == DUP_PRO_Recommended_Fix_Type::Text)
				{
					array_push($text_fix_strings, $fix->parameter1);
				}
			}
			
			return $text_fix_strings;
		}
		
        public static function &get_instance()
        {
			if(isset($GLOBALS[self::NAME_IN_GLOBALS]) == false)
			{			
				/* @var $system_globals DUP_PRO_System_Global_Entity */
				$system_global = null;
			
				$system_globals = DUP_PRO_JSON_Entity_Base::get_by_type(get_class());

				if (count($system_globals) > 0)
				{
					$system_global = $system_globals[0];
				}
				else
				{
					DUP_PRO_U::log_error("System Global entity is null!");
				}
				
				$GLOBALS[self::NAME_IN_GLOBALS] = $system_global;
			}

            return $GLOBALS[self::NAME_IN_GLOBALS];
        }
    }
}