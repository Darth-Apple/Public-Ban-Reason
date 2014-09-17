This plugin allows MyBB to display ban reasons publicly on the forum. This plugin can be configured to display ban reasons and expiration dates on the profiles, signatures, or both. (When signature mode is enabled, the user's signature is replaced with the ban reason, which may be useful for removing spam links from spambots. The signatures are not deleted, so as soon as the ban expires, the user's actual signature will return. )

This plugin also allows you to configure which usergroups this plugin is enabled for, which may be useful if you have multiple banned usergroups. Note that the ban reasons will only display if the user is banned and is in one of the enabled usergroups. If you have enabled the registered group, for example, this plugin will not affect unbanned users. In general, it's safe to leave all usergroups enabled. If this plugin is installed on MyBB 1.6, enter -1 to enable all usergroups. 

Installation: 
	
	 - Upload the contents of /Upload to your MyBB root directory
	 - Activate via ACP -> Configuration -> Plugins
	 - This plugin was designed for MyBB 1.8, but is backwards compatible with MyBB 1.6 forums as well. 

Replacing signatures with ban reasons: 
	
	- This plugin allows you to replace signatures with the ban reason on the postbit. The user's actual signature will return when they are unbanned. This can be disabled in the plugin's settings. (ACP -> Configuration -> Public Ban Reason)
	- This plugin does not honor post-specific settings to hide or display the user signature. The ban reason displays on all posts that the user has made, regardless of their personal signature settings. Their signature settings will return once they are unbanned. 
	- When signature mode is enabled, the signature will be hidden on the user profile. 
	
Usergroup Permissions: 
	
	- The ban reason will only display if two conditions are met. 
		1) The user is banned
		2) The user resides in an enabled usergroup
		
	- This setting is primarily useful for controlling which banned usergroups the ban reason will display for. For example, if you want the ban reasons to show for spambots and for regular banned users, but don't want to show ban reasons for users removed for sensitive reasons, you can set two ban groups and only enable the primary banned usergroup via the plugin settings. 
	- MyBB apparently only checks the primary usergroup to detect if a user is banned. This plugin, by contrast, will consider you banned if you reside in any usergroup that is a banned usergroup. For example, if your primary usergroup is "Registered," but you also reside in the "Banned" usergroup as an additional group, this plugin will consider you banned. 
	 
Licence & Copyright: 

 /*     This file is part of Public Ban
  
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
