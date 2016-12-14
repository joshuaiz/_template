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

/*
 * Lower level utilities - no dependencies on anything else
 */

if (!class_exists('DUP_PRO_Low_U'))
{

    /**
     * @copyright 2016 Snap Creek LLC
	 * Required for low level utility methods that json_encode requires
     */
    class DUP_PRO_Low_U
    {        
        public static function get_public_properties($object)
        {
            $publics = get_object_vars($object);
            unset($publics['id']);

            // Disregard anything that starts with '_'
            foreach ($publics as $key => $value)
            {
                if (self::starts_with($key, '_'))
                {
                    unset($publics[$key]);
                }
            }

            return $publics;
        }   
						
		public static function starts_with($haystack, $needle)
        {
            $length = strlen($needle);

            return (substr($haystack, 0, $length) === $needle);
        }  
		
		public static function is_valid_md5($md5_candidate)
		{
			return preg_match('/^[a-f0-9]{32}$/', $md5_candidate);
		}
    }
}
?>
