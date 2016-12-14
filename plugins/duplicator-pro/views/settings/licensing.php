<?php
global $wp_version;
global $wpdb;

require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/utilities/class.license.u.php');

$global = DUP_PRO_Global_Entity::get_instance();
$force_refresh = true;
$nonce_action = 'duppro-settings-licensing-edit';

$license_activation_response = null;

//SAVE RESULTS
if (isset($_POST['action']))
{    
    $action = $_POST['action'];
    switch($action)
    {
        case 'activate':            
            $submitted_license_key = trim($_REQUEST['_license_key']);
            update_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, $submitted_license_key);
            
			$license_activation_response = DUP_PRO_License_Utility::change_license_activation(true);
				
			switch($license_activation_response)
			{
				case DUP_PRO_License_Activation_Response::OK:
					$action_response = DUP_PRO_U::__("License Activated");
					break;
				
				case DUP_PRO_License_Activation_Response::POST_ERROR:
					$action_response = sprintf(DUP_PRO_U::__("Cannot communicate with snapcreek.com. Please see <a target='_blank' href='%s'>this FAQ entry</a> for possible causes and resolutions."), 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-08-q');
					break;
				
				case DUP_PRO_License_Activation_Response::INVALID_RESPONSE:
				default:
					$action_response = DUP_PRO_U::__('Error activating license.');
					break;
			}

            break;
        
        case 'deactivate':
			
			$license_activation_response = DUP_PRO_License_Utility::change_license_activation(false);
			
			switch($license_activation_response)
			{
				case DUP_PRO_License_Activation_Response::OK:
					$action_response = DUP_PRO_U::__("License Deactivated");
					break;
				
				case DUP_PRO_License_Activation_Response::POST_ERROR:
					$action_response = sprintf(DUP_PRO_U::__("Cannot communicate with snapcreek.com. Please see <a target='_blank' href='%s'>this FAQ entry</a> for possible causes and resolutions."), 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-08-q');
					break;
				
				case DUP_PRO_License_Activation_Response::INVALID_RESPONSE:
				default:
					$action_response = DUP_PRO_U::__('Error deactivating license.');
					break;
			}
            break;
     }
     
     $force_refresh = true;
}

$license_status = DUP_PRO_License_Utility::get_license_status($force_refresh);
$license_text_disabled = false;
$activate_button_text = DUP_PRO_U::__('Activate');     
$license_status_text_alt = '';

if($license_status == DUP_PRO_License_Status::Valid)
{
    $license_status_style = 'color:#509B18';
    $license_status_text = DUP_PRO_U::__('Status: Active');
    $activate_button_text = DUP_PRO_U::__('Deactivate');
    $license_text_disabled = true;
}
else if(($license_status == DUP_PRO_License_Status::Inactive))
{
    $license_status_style = 'color:#dd3d36;';	
    $license_status_text = DUP_PRO_U::__('Status: Inactive');
}
else if($license_status == DUP_PRO_License_Status::Site_Inactive)
{
	$license_status_style = 'color:#dd3d36;';	
	
	/* @var $global DUP_PRO_Global_Entity */
	$global = DUP_PRO_Global_Entity::get_instance();
	
	if($global->license_no_activations_left)
	{
		$license_status_text = DUP_PRO_U::__('Status: Inactive (out of site licenses)');
	}
	else
	{
		$license_status_text = DUP_PRO_U::__('Status: Inactive');
	}
}
else if($license_status == DUP_PRO_License_Status::Expired)
{
	$license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');				
	$renewal_url = 'https://snapcreek.com/checkout?edd_license_key=' . $license_key;				
	$license_status_style = 'color:#dd3d36;';    
	$license_status_text = sprintf('Your Duplicator Pro license key has expired so you aren\'t getting important updates! <a target="_blank" href="%1$s">Renew your license now</a>', $renewal_url);
}
else
{
    $license_status_string = DUP_PRO_License_Utility::get_license_status_string($license_status);
    $license_status_style = 'color:#dd3d36;';    
	$license_status_text  = DUP_PRO_U::__('Status: ') . $license_status_string . '<br/>';
	$license_status_text_alt  = DUP_PRO_U::__('If the license fails to activate please wait a few minutes and try again.');
	$license_status_text_alt .= '<br/><br/>';
    $license_status_text_alt .= sprintf(DUP_PRO_U::__('- Failure to activate after several attempts please review %1$sfaq activation steps%2$s'), '<a target="_blank" href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-005-q">', '</a>.<br/>');
	$license_status_text_alt .= sprintf(DUP_PRO_U::__('- To upgrade or renew your license visit %1$ssnapcreek.com%2$s'), '<a target="_blank" href="https://snapcreek.com">', '</a>.<br/>');
}
$license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');
?>

<form id="dup-settings-form" action="<?php echo self_admin_url('admin.php?page=' . DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ); ?>" method="post" data-parsley-validate>
    <?php wp_nonce_field($nonce_action); ?>
    <input type="hidden" name="action" value="save" id="action">
    <input type="hidden" name="page"   value="<?php echo DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ?>">
    <input type="hidden" name="tab"   value="licensing">

    <?php if ($license_activation_response === DUP_PRO_License_Activation_Response::OK) : ?>
        <div id="message" class="updated below-h2"><p><?php echo $action_response; ?></p></div>
    <?php elseif ($license_activation_response !== null) : ?>	
        <div id="message" class="error below-h2"><p><?php echo $action_response; ?></p></div>
    <?php endif; ?>

    <h3 class="title"><?php DUP_PRO_U::_e("Plugin") ?> </h3>
    <hr size="1" />
    <table class="form-table">
        <tr valign="top">           
            <th scope="row"><?php DUP_PRO_U::_e("Manage") ?></th>
            <td><?php echo sprintf(DUP_PRO_U::__('%1$s[Manage Licenses Online]%2$s'), '<a target="_blank" href="https://snapcreek.com/dashboard">', '</a>'); ?></td>
        </tr>		
        <tr valign="top">           
            <th scope="row"><label><?php DUP_PRO_U::_e("License Key"); ?></label></th>
            <td>
                <input type="text" class="wide-input" name="_license_key" id="_license_key" <?php DUP_PRO_U::echo_disabled($license_text_disabled); ?> value="<?php echo $license_key; ?>" />
                <p class="description">
                    <?php 
						echo "<span style='$license_status_style'>$license_status_text</span>"; 
						echo $license_status_text_alt;
					?>
                </p>
				<br/><br/>
				<button onclick="DupPro.Licensing.ChangeActivationStatus(<?php echo (($license_status != DUP_PRO_License_Status::Valid) ? 'true' : 'false'); ?>);return false;"><?php echo $activate_button_text; ?></button>
            </td>
        </tr>
    </table>   
</form>

<script type="text/javascript">
    jQuery(document).ready(function($) 
	{
        DupPro.Licensing = new Object();
        // which: 0=installer, 1=archive, 2=sql file, 3=log
        DupPro.Licensing.ChangeActivationStatus = function (activate) 
		{    
            if(activate){
                $('#action').val('activate');
            } 
            else  {
                $('#action').val('deactivate');
            }
            $('#dup-settings-form').submit();
        }
    });
</script>
