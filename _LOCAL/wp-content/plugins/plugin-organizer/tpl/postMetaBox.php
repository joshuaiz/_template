<script type="text/javascript" language="javascript">
	PO_reverse_toggle_buttons();
</script>

<?php 
$adminPage = '';
if (isset($_GET['page'])) {
	$adminPage = $_GET['page'];
}

?>
		
<?php
if (isset($errMsg) && $errMsg != "") {
	?>
	<h3 style="color: #CC0066;"><?php print $errMsg; ?></h3>
	<?php
}
	
if (isset($post) && is_array(get_option('PO_disabled_pt_plugins_'.get_post_type($post->ID)))) {
	?>
	<div id="PO-pt-override-msg-container">
		Settings for this post type have been overridden by the post type settings.  You can edit them by going <a href="<?php print get_admin_url(); ?>admin.php?page=PO_pt_plugins&PO_target_post_type=<?php print get_post_type($post->ID); ?>">here</a>.  You can also override them by checking the box below and saving the post.
		<br /><input type="checkbox" id="PO-activate-pt-override" name="PO_activate_pt_override" value="1" <?php print ($ptOverride == "1")? 'checked="checked"':''; ?>>Override Post Type settings
		<a href="#" onclick="PO_display_ui_dialog('Override Post Type Settings', 'By checking this box the changes you make here will not be overwritten by the settings that have been set for the <?php print get_post_type($post->ID); ?> post type.  You will be able to see the plugins disabled/enabled on this page and make changes to them.');return false;">
		  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
		</a>
		<?php if ($ptOverride == 0) { ?>
			<style type="text/css">
				#PO-post-meta-box-wrapper {display:none;}
			</style>
		<?php } else { ?>
			<style type="text/css">
				#PO-pt-override-msg-container {display:none;}
			</style>
		<?php } ?>
		
	</div>
	<?php
}
?>
<div id="PO-post-meta-box-wrapper" class="PO-content-wrap">
	<?php
	if ($adminPage != 'PO_search_plugins' && $adminPage != 'PO_global_plugins') { ?>
		<?php if(isset($post) && get_post_type($post->ID) == 'plugin_filter') { ?>
			<div class="metaBoxLabel">
				Name
			</div>
			<div class="metaBoxContent">
				<input type="text" class="poFilterNameInput" size="25" name="filterName" value="<?php print $filterName; ?>">
			</div>
			<div class="metaBoxLabel">
				Permalink
			</div>
			<div class="metaBoxContent">
				<input type="text" class="poPermalinkInput" size="25" name="permalinkFilter" value="<?php print ($permalinkFilter != "") ? (($secure == 1)? "https://":"http://") . $permalinkFilter : ""; ?>">
			</div>
		<?php } ?>

		<div id="settingsMetaBox" class="metaBoxContent">
			<div class="pluginListHead">Settings<?php if(isset($ajaxSaveFunction)) { ?><input type=button name=submit value="Save" onmousedown="<?php print $ajaxSaveFunction; ?>" class="PO-ajax-save-btn"><?php } ?></div>
			<?php if ($adminPage == 'PO_pt_plugins') { ?>
				<div style="padding-left: 10px;">
				Post Type: <select id="PO-selected-post-type" name="PO_selected_post_type">
				<?php
					$supportedPostTypes = get_option("PO_custom_post_type_support");
					if (!is_array($supportedPostTypes)) {
						$supportedPostTypes = array();
					}
					if (isset($_REQUEST['PO_target_post_type'])) {
						$targetPostType = $_REQUEST['PO_target_post_type'];
					} else {
						$targetPostType = '';
					}
					
					foreach($supportedPostTypes as $postType) {
						print '<option value="' . $postType . '" ' . (($targetPostType == $postType)? 'selected="selected" ':'') . '>' . $postType . '</option>';
					}
				?>
				</select>
				</div>
				<hr>
				<input type="button" class="button" style="float: left;margin: 5px;" id="resetPostTypeSettings" value="Reset settings for this post type" onclick="PO_reset_pt_settings();">
				<div style="float: left;margin: 10px 5px 0px 0px;">
					<input type="checkbox" id="PO-reset-all-pt" name="PO-reset-all-pt" value="1"><label for="PO-reset-all-pt">Reset All</label>
				</div>
				<a href="#" onclick="PO_display_ui_dialog('Reset all matching posts', 'By checking this box all posts that match the selected post type will be reset.  If the box isn\'t checked the post type setting will be reset but the individual posts will still keep the settings until they are changed individually.  You can go directly to each post matching this post type and override this setting.  Then the changes you make here will not affect that post.');return false;">
					<span class="dashicons PO-dashicon dashicons-editor-help"></span>
				 </a>
				<div style="clear: both;"></div>
			<?php } else { ?>
				<?php if (isset($post)) { ?>
					<input type="checkbox" id="affectChildren" name="affectChildren" value="1" <?php print ($affectChildren == "1")? 'checked="checked"':''; ?>>Also Affect Children
					<a href="#" onclick="PO_display_ui_dialog('Also Affect Children', 'By checking this box the plugins disabled or enabled for this page will be used for its children if they have nothing set.');return false;">
					  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
					</a>
					<hr>
					<?php if(isset($post) && in_array(get_post_type($post->ID), get_option('PO_custom_post_type_support'))) { ?>
						<input type="checkbox" id="PO-pt-override" name="PO_pt_override" value="1" <?php print ($ptOverride == "1")? 'checked="checked"':''; ?>>Override Post Type settings
						<a href="#" onclick="PO_display_ui_dialog('Override Post Type Settings', 'By checking this box the changes you make here will not be overwritten by the settings that have been set for the <?php print get_post_type($post->ID); ?> post type.');return false;">
						  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
						</a>
						<hr>
					<?php } ?>
					<input type="button" class="button" style="margin: 5px;" id="resetPostSettings" value="Reset settings for this post" onclick="PO_reset_post_settings(<?php print $post->ID; ?>);">
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>
	<div id="pluginContainer" class="metaBoxContent">
		<div class="pluginListHead">Plugins<?php if(isset($ajaxSaveFunction)) { ?><input type=button name=submit value="Save" onmousedown="<?php print $ajaxSaveFunction; ?>" class="PO-ajax-save-btn"><?php } ?></div>
		<div id="PO-plugin-legend-wrap">
			<div id="PO-plugin-legend-header">
				Legend:
			</div>
			<div id="PO-plugin-legend-active">Active</div>
			<div id="PO-plugin-legend-group">Group</div>
			<div id="PO-plugin-legend-inactive">Inactive</div>
			<div id="PO-plugin-legend-global">Globally Disabled</div>
			<div style="clear: both;"></div>
			<div id="PO-plugin-legend-standard-indicator">Disabled Standard: <img src="<?php print $this->PO->urlPath; ?>/image/sm-red-dot.png" /></div>
			<div id="PO-plugin-legend-mobile-indicator">Disabled Mobile: <img src="<?php print $this->PO->urlPath; ?>/image/sm-blue-dot.png" /></div>
			<div id="PO-plugin-legend-notes">Drag plugins and groups from the container to the left and drop them on the container to the right to disable them.  Drag plugins from the right and drop them on the container to the left to enable them.</div>
		</div>
		<div id="PO-all-plugin-wrap" class="outerPluginWrap">
			<div class="pluginListHead">Available Items</div>
			<div class="pluginListSubHead plugins">Plugins</div>
			<?php
			foreach ($plugins as $key=>$plugin) {
				$pluginWrapClass = (in_array($key, $activeSitewidePlugins) || in_array($key, $activePlugins))? "activePluginWrap" : "inactivePluginWrap";
				$pluginWrapClass .= ((in_array($key, $globalPlugins) && !in_array($key, $enabledPluginList)) || in_array($key, $disabledPluginList))? ' disabledStandard' : '';
				$pluginWrapClass .= ((in_array($key, $globalMobilePlugins) && !in_array($key, $enabledMobilePluginList)) || in_array($key, $disabledMobilePluginList))? ' disabledMobile' : '';
				?>
				<div class="pluginWrap <?php print $pluginWrapClass; ?>">
					<div class="disabledMobileIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-blue-dot.png" /></div>
					<div class="disabledStandardIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-red-dot.png" /></div>
					<div class="pluginNameContainer">
						<input type="hidden" class="PO-plugin-id" value="<?php print $key; ?>" />
						<?php print $plugin['Name']; ?>
					</div>
					<div style="clear: both;"></div>
				</div>
				<?php
			} ?>

			<div class="pluginListSubHead groups">Groups</div>
			<?php if (sizeOf($groupList) > 0) {
				foreach ($groupList as $key=>$group) {
					$groupWrapClass = ((in_array($group->ID, $globalGroups) && !in_array($group->ID, $enabledGroupList)) || in_array($group->ID, $disabledGroupList))? 'disabledStandard' : '';
					$groupWrapClass .= ((in_array($group->ID, $globalMobileGroups) && !in_array($group->ID, $enabledMobileGroupList)) || in_array($group->ID, $disabledMobileGroupList))? ' disabledMobile' : '';
					?>
					<div class="groupWrap <?php print $groupWrapClass; ?>">
						
						<div class="disabledMobileIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-blue-dot.png" /></div>
						<div class="disabledStandardIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-red-dot.png" /></div>
						<div class="pluginNameContainer">
							<input type="hidden" class="PO-group-id" value="<?php print $group->ID; ?>" />
							<?php 
							$membersTip = '';
							$groupMembers = get_post_meta($group->ID, "_PO_group_members", $single=true);
							if (is_array($groupMembers)) {
								foreach($groupMembers as $plugin) {
									$membersTip .= $plugins[$plugin]['Name'].'__nl__';
								}
							}
							?>
							<a href="#" class="PO-group-members" title="<?php print $membersTip; ?>"><?php print $group->post_title; ?></a>
						</div>
						<div style="clear: both;"></div>
					</div>
					<?php
				}
			} ?>
		</div>
		
		<div id="PO-disabled-std-plugin-wrap" class="outerPluginWrap disabledPlugins">
			<div class="pluginListHead">Disabled Standard<div class="disabledListToggle fa fa-minus-square-o"></div></div>
			<div class="disabledList">
				<div class="disableAllContainer">
					<div style="padding: 5px 0px 0px 0px;">
						Plugins: <input type="button" id="PO-add-all-std-plugins-button" class="button button-small toggle-button" value="Add All" onclick="PO_add_all('plugin', 'std');" />
						<input type="button" id="PO-remove-all-std-plugins-button" class="button button-small toggle-button" value="Remove All" onclick="PO_remove_all('plugin', 'std');" />
					</div>
					<div style="padding: 5px 0px;">
						Groups: <input type="button" id="PO-add-all-std-groups-button" class="button button-small toggle-button" value="Add All" onclick="PO_add_all('group', 'std');" />
						<input type="button" id="PO-remove-all-std-groups-button" class="button button-small toggle-button" value="Remove All" onclick="PO_remove_all('group', 'std');" />
					</div>
				</div>
				<div class="pluginListSubHead plugins">Plugins</div>
				<?php
				foreach ($plugins as $key=>$plugin) {
					$pluginWrapClass = (in_array($key, $activeSitewidePlugins) || in_array($key, $activePlugins))? "activePluginWrap" : "inactivePluginWrap";
					$pluginWrapClass .= (in_array($key, $globalPlugins))? " globalPluginWrap": "";
					

					if ((in_array($key, $globalPlugins) && !in_array($key, $enabledPluginList)) || in_array($key, $disabledPluginList)) {
						?>
						<div class="pluginWrap <?php print $pluginWrapClass; ?>">
							<input type="hidden" class="PO-plugin-id" value="<?php print $key; ?>" />
							<input type="hidden" class="PO-disabled-item-id PO-disabled-std-plugin-list" name="PO_disabled_std_plugin_list[]" value="<?php print $key; ?>" />
							<?php print $plugin['Name']; ?>
						</div>
						<?php
					}
				}
				?>
				<div class="pluginListSubHead groups">Groups</div>
				<?php
				foreach ($groupList as $key=>$group) {
					if ((in_array($group->ID, $globalGroups) && !in_array($group->ID, $enabledGroupList)) || in_array($group->ID, $disabledGroupList)) {
						$groupWrapClass = (in_array($group->ID, $globalGroups))? "globalGroupWrap": "";
						?>
						<div class="groupWrap <?php print $groupWrapClass; ?>">
							<input type="hidden" class="PO-group-id" value="<?php print $group->ID; ?>" />
							<input type="hidden" class="PO-disabled-item-id PO-disabled-std-group-list" name="PO_disabled_std_group_list[]" value="<?php print $group->ID; ?>" />
							<?php 
							$membersTip = '';
							$groupMembers = get_post_meta($group->ID, "_PO_group_members", $single=true);
							if (is_array($groupMembers)) {
								foreach($groupMembers as $plugin) {
									$membersTip .= $plugins[$plugin]['Name'].'__nl__';
								}
							}
							?>
							<a href="#" class="PO-group-members" title="<?php print $membersTip; ?>"><?php print $group->post_title; ?></a>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>

		<?php if (get_option('PO_disable_mobile_plugins') == 1) { ?>
			<div style="clear: right;"></div>
			<div id="PO-disabled-mobile-plugin-wrap" class="outerPluginWrap disabledPlugins">
				<div class="pluginListHead">Disabled Mobile<div class="disabledListToggle fa fa-minus-square-o"></div></div>
				<div class="disabledList">
					<div class="disableAllContainer">
						<div style="padding: 5px 0px 0px 0px;">
							Plugins: <input type="button" id="PO-add-all-mobile-plugins-button" class="button button-small toggle-button" value="Add All" onclick="PO_add_all('plugin', 'mobile');" />
							<input type="button" id="PO-remove-all-mobile-plugins-button" class="button button-small toggle-button" value="Remove All" onclick="PO_remove_all('plugin', 'mobile');" />
						</div>
						<div style="padding: 5px 0px;">
							Groups: <input type="button" id="PO-add-all-mobile-groups-button" class="button button-small toggle-button" value="Add All" onclick="PO_add_all('group', 'mobile');" />
							<input type="button" id="PO-remove-all-mobile-groups-button" class="button button-small toggle-button" value="Remove All" onclick="PO_remove_all('group', 'mobile');" />
						</div>
					</div>
					<div class="pluginListSubHead plugins">Plugins</div>
					<?php
					foreach ($plugins as $key=>$plugin) {
						$pluginWrapClass = (in_array($key, $activeSitewidePlugins) || in_array($key, $activePlugins))? "activePluginWrap" : "inactivePluginWrap";
						$pluginWrapClass .= (in_array($key, $globalMobilePlugins))? " globalPluginWrap": "";
						
						if ((in_array($key, $globalMobilePlugins) && !in_array($key, $enabledMobilePluginList)) || in_array($key, $disabledMobilePluginList)) {
							?>
							<div class="pluginWrap <?php print $pluginWrapClass; ?>">
								<input type="hidden" class="PO-plugin-id" value="<?php print $key; ?>" />
								<input type="hidden" class="PO-disabled-item-id PO-disabled-mobile-plugin-list" name="PO_disabled_mobile_plugin_list[]" value="<?php print $key; ?>" />
								<?php print $plugin['Name']; ?>
							</div>
							<?php
						}
					}
					?>

					
					<div class="pluginListSubHead groups">Groups</div>

					<?php
					foreach ($groupList as $key=>$group) {
						if ((in_array($group->ID, $globalMobileGroups) && !in_array($group->ID, $enabledMobileGroupList)) || in_array($group->ID, $disabledMobileGroupList)) {
							$groupWrapClass = (in_array($group->ID, $globalGroups))? "globalGroupWrap": "";
							?>
							<div class="groupWrap <?php print $groupWrapClass; ?>">
								<input type="hidden" class="PO-group-id" value="<?php print $group->ID; ?>" />
								<input type="hidden" class="PO-disabled-item-id PO-disabled-std-group-list" name="PO_disabled_std_group_list[]" value="<?php print $group->ID; ?>" />
								<?php 
								$membersTip = '';
								$groupMembers = get_post_meta($group->ID, "_PO_group_members", $single=true);
								if (is_array($groupMembers)) {
									foreach($groupMembers as $plugin) {
										$membersTip .= $plugins[$plugin]['Name'].'__nl__';
									}
								}
								?>
								<a href="#" class="PO-group-members" title="<?php print $membersTip; ?>"><?php print $group->post_title; ?></a>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		<?php } ?>
		<div style="clear: both;"></div>
	</div>
	<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>
<input type="hidden" name="poSubmitPostMetaBox" value="1" />