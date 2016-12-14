<?php
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/class.constants.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/utilities/class.u.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'classes/entities/class.global.entity.php');

if (!defined('ABSPATH'))
{
	
	exit();
}

if (!class_exists('EDD_SL_Plugin_Updater'))
{
	
	require_once (DUPLICATOR_PRO_PLUGIN_PATH . 'lib/edd/EDD_SL_Plugin_Updater.php');
}



if (!class_exists('DUP_PRO_Enhanced_Plugin_Updater'))
{

	/**
	 * Enhanced (Caching) Plugin Updater
	 *
	 * @author Snap Creek Software <admin@snapcreek.com>
	 * @copyright 2016 Snap Creek LLC
	 */
	class DUP_PRO_Enhanced_Plugin_Updater extends EDD_SL_Plugin_Updater
	{
		public function __construct($_api_url, $_plugin_file, $_api_data, $slug)
		{
			parent::__construct($_api_url, $_plugin_file, $_api_data);

			$this->slug = $slug;
		}

		protected function api_request($_action, $_data)
		{
			//		DUP_PRO_U::log("####api request");
			/* @var $global DUP_PRO_Global_Entity */
			$global = DUP_PRO_Global_Entity::get_instance();

			$time = $global->last_edd_api_timestamp;

			//		DUP_PRO_U::log("####time() is " . time());
			//		DUP_PRO_U::log("####Last check time was $time");

			if (($time === 0) || ($time < (time() - DUP_PRO_Constants::EDD_API_CACHE_TIME)))
			{
				//			DUP_PRO_U::log("####cache ran out");
				$api_response = null;
			}
			else
			{
				//			DUP_PRO_U::log("####using cached response");
				$api_response = $global->last_edd_api_response;
			}

			// Retrieve and deserialize
			if ($api_response === null)
			{
				//			DUP_PRO_U::log("####api response is null so making request");
				$api_response = parent::api_request($_action, $_data);

				if ($api_response)
				{
					$global->last_edd_api_response = serialize($api_response);
					$global->last_edd_api_timestamp = time();

					$global->save();

					//				DUP_PRO_U::log("####new response value retrieved and stored into global");
				}
			}
			else
			{
				//			DUP_PRO_U::log("####already had cached response so using that");
				$api_response = unserialize($api_response);
			}

			//		DUP_PRO_U::log_object('Response being sent back=', $api_response);
			return $api_response;
		}

		public function recheck()
		{

			/* @var $global DUP_PRO_Global_Entity */
			$global = DUP_PRO_Global_Entity::get_instance();

			$global->last_edd_api_timestamp = 0;

			$global->save();

			set_site_transient('update_plugins', null);
		}

	}

}
?>