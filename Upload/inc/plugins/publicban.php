<?php

 /*     This file is part of Public Ban
  * 
    Public Ban is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Public Ban is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Public Ban.  If not, see <http://www.gnu.org/licenses/>. */

	if(!defined("IN_MYBB")) {
	    die("Hacking Attempt.");
	}
	
	$plugins->add_hook("member_profile_end", "publicban_parse_profile");
	$plugins->add_hook("postbit", "publicban_parse_postbit");
	
	function publicban_info() {
		global $lang;
		$lang->load("publicban");
		return array (
			'name'			=> $lang->publicban,
			'description'	=> $lang->publicban_desc,
			'website'		=> 'http://community.mybb.com',
			'author'		=> 'Darth Apple',
			'authorsite'	=> 'http://www.makestation.net',
			'version'		=> '1.0',
			"compatibility"	=> "16*, 18*"
		);
	}
	
	
	function publicban_activate () {
		global $lang, $db, $mybb;
		require MYBB_ROOT.'inc/adminfunctions_templates.php';
		find_replace_templatesets('member_profile', '#{\$warning_level}#', '{$warning_level} <!-- PublicBan -->{$publicban}<!-- /PublicBan -->');
		
		$lang->load("publicban");
		
		$templates = array();
		
		$templates['publicban_profile'] = '
<tr>
		<td class="{$bg_color}" style="width: 30%;"><strong>{$lang->publicban}</strong></td>
		<td class="{$bg_color}" style="color: green; ">{$banreason} {$timeremaining}</td>
</tr>
		'; 
		
		$templates['publicban_signature'] = '
<div style="color: #800F0F; padding:8px 35px 8px 14px; background: #FFF6BF; text-shadow:0 1px 0 rgba(255, 255, 255, 0.5); border: 1px solid #FFDF5E; -webkit-border-radius:4px;-moz-border-radius: 4px;border-radius:4px;">
		{$lang->publicban_sig_prefix} {$banreason} {$timeremaining}
</div>
		'; 
		
		foreach($templates as $title => $template_new){
			$template = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template_new), 'sid' => '-1', 'dateline' => TIME_NOW, 'version' => '1800');
			$db->insert_query('templates', $template);
		}
		
		$setting_group = array (
			'name' => 'publicban', 
			'title' => $db->escape_string($lang->publicban),
			'description' => $db->escape_string($lang->publicban_desc),
			'disporder' => $rows+3,
			'isdefault' => 0
		); 
		
		$group['gid'] = $db->insert_query("settinggroups", $setting_group); // inserts new group for settings into the database. 
		
		$settings = array();
		
		$settings[] = array(
			'name' => 'publicban_enabled',
			'title' => $db->escape_string($lang->publicban_enable),
			'description' => $db->escape_string($lang->publicban_enable_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 1,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
		
		$settings[] = array(
			'name' => 'publicban_liftdate',
			'title' => $db->escape_string($lang->publicban_liftdate),
			'description' => $db->escape_string($lang->publicban_liftdate_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 2,
			'isdefault' => 1,
			'gid' => $group['gid']
		);		
		
		$settings[] = array(
			'name' => 'publicban_enabled_profile',
			'title' => $db->escape_string($lang->publicban_profile),
			'description' => $db->escape_string($lang->publicban_profile),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 3,
			'isdefault' => 1,
			'gid' => $group['gid']
		);		
		
		$settings[] = array(
			'name' => 'publicban_enabled_signature',
			'title' => $db->escape_string($lang->publicban_signature),
			'description' => $db->escape_string($lang->publicban_signature),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 4,
			'isdefault' => 1,
			'gid' => $group['gid']
		);				
		
		// ensures compatibility with both MyBB 1.6 and 1.8. 
		if(strpos($mybb->version, "1.8.") !== false)  {
			$grouptype = "groupselect";
			$groupdesc = $lang->publicban_groups_desc; 
		}			
		else {
			$grouptype = "text";
			$groupdesc = $lang->publicban_groups_16;
		}
		
		$settings[] = array(
			'name' => 'publicban_groups',
			'title' => $db->escape_string($lang->publicban_groups),
			'description' => $db->escape_string($groupdesc),
			'optionscode' => $grouptype,
			'value' => '-1',
			'disporder' => 5,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
		
		foreach($settings as $array => $setting) {
			$db->insert_query("settings", $setting);
		}
		rebuild_settings();
	}
	
	
	function publicban_deactivate () {
		global $templates, $db;
		require MYBB_ROOT.'inc/adminfunctions_templates.php';
		find_replace_templatesets('member_profile', '#\<!--\sPublicBan\s--\>(.+)\<!--\s/PublicBan\s--\>#is', '', 0);
		
		$templates = array('publicban_profile', 'publicban_signature'); // remove templates
		foreach($templates as $template) {
			$db->delete_query('templates', "title = '{$template}'");
		}
		
		$query = $db->simple_select('settinggroups', 'gid', 'name = "publicban"'); // remove settings
		$groupid = $db->fetch_field($query, 'gid');
		$db->delete_query('settings','gid = "'.$groupid.'"');
		$db->delete_query('settinggroups','gid = "'.$groupid.'"');
		rebuild_settings();	
	}
	
	
	function publicban_parse_profile () {
		global $templates, $db, $mybb, $lang, $bg_color, $publicban, $signature;
		require_once MYBB_ROOT."inc/class_parser.php";	
		// parse ban reason
				
		if (($mybb->settings['publicban_enabled'] != 1) || ($mybb->settings['publicban_enabled_profile'] == 0)) {
			return; 
		}
		
		$lang->load("publicban");
		
		$parser_options = array(
			"allow_html" => 0,
			"allow_mycode" => 1,
			"allow_smilies" => 1,
			"allow_imgcode" => 0,
			"filter_badwords" => 1
		);
		$parser = new postParser();
	
		if ($bg_color == "trow2") $bg_color = "trow1";
		
		if ($mybb->input['uid']) {
			$profileID = (int) $mybb->input['uid'];
		}
		
		else {
			$profileID = (int) $mybb->user['uid']; // user is viewing own profile
		}
		
		$query = $db->query("
			SELECT u.username, u.usergroup, u.additionalgroups, b.reason, b.lifted, b.bantime
			FROM ".TABLE_PREFIX."users u
			LEFT JOIN " . TABLE_PREFIX . "banned b ON b.uid = u.uid
			WHERE u.uid =". (int) $profileID);

		while($data = $db->fetch_array($query)) {
			$parser_options['me_username'] = $data['username'];
			$banreason = $parser->parse_message($data['reason'], $parser_options);
			$usergroup = $data['usergroup'];
			$additionalgroups = $data['additionalgroups'];
			$lifted = $data['lifted']; 
			$bantime = $data['bantime'];
		}	
		
		
		if(!$banreason) $banreason = $lang->publicban_none;
		
		if ((!publicban_permissions($usergroup, $additionalgroups)) || !publicban_isbanned($usergroup, $additionalgroups)) {
			return; // Public Ban Reason is not enabled for this usergroup, or the user is not banned. 
		}
		
		$timeremaining = publicban_time_remaining($lifted, $bantime); // format time remaining using the same format used for the mod CP. 
		
		if($mybb->settings['publicban_enabled_signature'] == 1) {
			$signature = null; // remove the signature from the profile if the signature is being replaced by the ban reason. This may be useful for hiding links created by spambots. 
		}
		
		eval("\$publicban = \"".$templates->get("publicban_profile")."\";");
	}
	
	
	function publicban_parse_postbit (&$post) {
		global $templates, $mybb, $db, $lang;
		
		if(publicban_isbanned($post['usergroup'], $post['additionalgroups'])) {
			$lang->load("publicban");
			require_once MYBB_ROOT."inc/class_parser.php";		
			
			$parser_options = array(
				"allow_html" => 0,
				"allow_mycode" => 1,
				"allow_smilies" => 1,
				"allow_imgcode" => 0,
				"me_username" => $post['username'],
				"filter_badwords" => 1
			);
			$parser = new postParser();
				
			if (($mybb->settings['publicban_enabled'] != 1) || ($mybb->settings['publicban_enabled_signature'] == 0)) {
				return; 
			}
			
			$query = $db->query("
				SELECT reason, lifted, bantime
				FROM ".TABLE_PREFIX."banned
				WHERE uid =". (int) $post['uid']);
	
			while($data = $db->fetch_array($query)) {
				$banreason = $data['reason'];	
				$lifted = $data['lifted'];
				$bantime = $data['bantime'];					
			}	
			
			if(!$banreason) {
				$banreason = $lang->publicban_none;
			}
			
			if (!publicban_permissions($post['usergroup'], $post['additionalgroups'])) {
				return; // Public Ban Reason is not enabled for this usergroup. 
			}
			
			$timeremaining = publicban_time_remaining($lifted, $bantime); // format time remaining using the same format used for the mod CP. 		

			$banreason = $parser->parse_message($banreason, $parser_options);
			eval("\$post['signature'] = \"".$templates->get("publicban_signature")."\";");
			eval("\$post['signature'] = \"".$templates->get("postbit_signature")."\";");
		}
		
		return $post;
	}
	
	
	function publicban_permissions ($usergroup, $additionalgroups) {
		global $mybb; 
		$display_groups = $mybb->settings['publicban_groups'];
		if (empty($display_groups)) {
			return false; // no need to check for permissions if no groups are allowed. 
		}
		if ($display_groups == "-1") {
			return true; // no need to check for permissions if all groups are allowed. 
		}
		
		$allowed = explode(",", $display_groups);
		$groups = array();
		$groups[0] = (int)$usergroup; 
		$add_groups = explode(",", $additionalgroups);
		$count = 1;
		foreach($add_groups as $new_group) {
			$groups[$count] = $new_group;
			$count++;
		}
		foreach ($allowed as $allowed_group) {
			if (in_array($allowed_group, $groups)) {
				return true;
			}
		}
		return false;
	}
	
	function publicban_isbanned ($usergroup, $additionalgroups) {
		// Note: MyBB apparently only checks the primary usergroup. This function checks every usergroup. If the user is in any banned usergroup, this plugin will treat the user as a banned user. 
		global $cache; 
		$usergroups_cache = $cache->read("usergroups");	
		
		$groups = array();
		$groups[0] = (int)$usergroup; 
		$add_groups = explode(",", $additionalgroups);
		$count = 1;
		foreach($add_groups as $new_group) {
			$groups[$count] = $new_group; // appends additional groups to merge the usergroups and additionalgroups into one array. 
			$count++;
		}
		foreach ($groups as $group) {
			if($usergroups_cache[$group]['isbannedgroup'] == 1) {
				return true;
			}
		}
		return false;
	}	
	
	function publicban_time_remaining ($lifted, $bantime) {
		global $mybb, $lang;
		if ($mybb->settings['publicban_liftdate'] == 1) {
			$lang->load("modcp");
			$color = null;
			
			if(($lifted == 'perm') || ($lifted == '') || ($bantime == 'perm') || ($bantime == '---')) {
				$timeremaining = '('.$lang->permanent.')';
			}
			else {
				$remaining = $lifted-TIME_NOW;
				$timeremaining = nice_time($remaining, array('short' => 1, 'seconds' => false))."";
	
				if($remaining < 3600) {
					$timeremaining = "({$timeremaining} {$lang->ban_remaining})";
				}
				else if($remaining < 86400) {
					$timeremaining = "({$timeremaining} {$lang->ban_remaining})";
				}
				else if($remaining < 604800) {
					$timeremaining = "({$timeremaining} {$lang->ban_remaining})";
				}
				else {
					$timeremaining = "({$timeremaining} {$lang->ban_remaining})";
				}
			}
		}
		else {
			$timeremaining = null; // no unwanted PHP warnings on weird hosts. 
		}	
		return $timeremaining;
	}
