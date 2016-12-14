<?php
global $wpdb;
if ( current_user_can( 'activate_plugins' ) ) {
	?>
	<script type="text/javascript" language="javascript">
		function PO_submit_pt_plugins(){
			var disabledList = new Array();
			var disabledMobileList = new Array();
			var disabledGroupList = new Array();
			var disabledMobileGroupList = new Array();
			var selectedPostType = jQuery('select#PO-selected-post-type').val();
			jQuery('.PO-disabled-std-plugin-list').each(function() {
				disabledList[disabledList.length] = jQuery(this).val();
			});

			jQuery('.PO-disabled-mobile-plugin-list').each(function() {
				disabledMobileList[disabledMobileList.length] = jQuery(this).val();
			});

			jQuery('.PO-disabled-std-group-list').each(function() {
				disabledGroupList[disabledGroupList.length] = jQuery(this).val();
			});

			jQuery('.PO-disabled-mobile-group-list').each(function() {
				disabledMobileGroupList[disabledMobileGroupList.length] = jQuery(this).val();
			});
			
			var postVars = { 'PO_disabled_std_plugin_list[]': disabledList, 'PO_disabled_mobile_plugin_list[]': disabledMobileList, 'PO_disabled_std_group_list[]': disabledGroupList, 'PO_disabled_mobile_group_list[]': disabledMobileGroupList, 'selectedPostType': selectedPostType, PO_nonce: '<?php print $this->PO->nonce; ?>' };
			PO_submit_ajax('PO_save_pt_plugins', postVars, '#PO-pt-settings', PO_get_pt_plugins);
		}

		function PO_add_saved_items(sourceType, targetType, values) {
			PO_remove_all(sourceType, targetType);
			jQuery(jQuery('#PO-all-plugin-wrap .'+sourceType+'Wrap').get().reverse()).each(function() {
				var newElement = jQuery(this).clone();
				newElement.append('<input type="hidden" class="PO-disabled-item-id PO-disabled-'+targetType+'-'+sourceType+'-list" name="PO_disabled_'+targetType+'_'+sourceType+'_list[]" value="'+newElement.find('.PO-'+sourceType+'-id').val()+'" />');
				
				var idElement = newElement.find('.PO-'+sourceType+'-id');
				if (jQuery.inArray(idElement.val(), values[0]) > -1 || (jQuery.inArray(idElement.val(), values[2]) > -1 && jQuery.inArray(idElement.val(), values[1]) == -1)) {
					if (jQuery.inArray(idElement.val(), values[2]) > -1) {
						newElement.addClass('global'+sourceType.charAt(0).toUpperCase()+sourceType.slice(1)+'Wrap');
					}
					
					jQuery('#PO-disabled-'+targetType+'-plugin-wrap').find('.pluginListSubHead.'+sourceType+'s').after(newElement);
					
					if (targetType == 'std') {
						PO_activate_indicator(sourceType, 'Standard', idElement.val());
					} else {
						PO_activate_indicator(sourceType, 'Mobile', idElement.val());
					}
				}
			});
			PO_attach_ui_handlers();
		}
		
		function PO_get_pt_plugins() {
			var selectedPostType = jQuery('select#PO-selected-post-type').val();
			PO_toggle_loading('#PO-pt-settings');
			jQuery.post(encodeURI(ajaxurl + '?action=PO_get_pt_plugins'), {'selectedPostType': selectedPostType, PO_nonce: '<?php print $this->PO->nonce; ?>' }, function (result) {
				var pluginLists = jQuery.parseJSON(result);
				PO_add_saved_items('plugin', 'std', new Array(pluginLists[0], pluginLists[1], globalPlugins['std_plugins']));
				PO_add_saved_items('plugin', 'mobile', new Array(pluginLists[2], pluginLists[3], globalPlugins['mobile_plugins']));
				PO_add_saved_items('group', 'std', new Array(pluginLists[4], pluginLists[5], globalPlugins['std_groups']));
				PO_add_saved_items('group', 'mobile',  new Array(pluginLists[6], pluginLists[7], globalPlugins['mobile_groups']));
				
				PO_toggle_loading('#PO-pt-settings');
				
			});
		}

		function PO_reset_pt_settings() {
			var selectedPostType = jQuery('select#PO-selected-post-type').val();
			if (confirm('Are you sure you want to reset the enabled/disabled plugins back to default for this post type?')) {
				if (jQuery('#PO-reset-all-pt').prop('checked')) {
					resetAll = 1;
				} else {
					resetAll = 0;
				}
				var postVars = {'selectedPostType': selectedPostType, PO_nonce: '<?php print $this->PO->nonce; ?>', PO_reset_all_pt: resetAll };
				PO_submit_ajax('PO_reset_pt_settings', postVars, '#PO-pt-settings', PO_get_pt_plugins);
			}
		}
		
		jQuery(function() {
			PO_toggle_loading('#PO-pt-settings');
			PO_get_pt_plugins();
			jQuery('#PO-selected-post-type').change(function() {
				PO_get_pt_plugins()
			});
		});
	</script>
	<?php
}
?>