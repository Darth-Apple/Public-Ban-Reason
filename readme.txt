This plugin allows MyBB to display ban reasons publicly on the forum. This plugin can be configured to display ban reasons and expiration dates on the profiles, signatures, or both. (When signature mode is enabled, the user's signature is replaced with the ban reason, which may be useful for removing spam links from spambots. The signatures are not deleted, so as soon as the ban expires, the user's actual signature will return. )

This plugin also allows you to configure which usergroups this plugin is enabled for, which may be useful if you have multiple banned usergroups. On MyBB 1.6 forums, enter -1 to select all usergroups (MyBB 1.8 has support for a better way to do this). Note that the ban reasons will only display if the user is banned and is in one of the enabled usergroups. If you have enabled the registered group, for example, this plugin will not affect unbanned users. In general, it's safe to leave all usergroups enabled. 

Installation: 
	
	 - Upload the contents of the /Upload directory to your MyBB root directory
	 - Activate via ACP -> Configurations -> Plugins
	 - This plugin was designed for 1.8, but is fully backwards compatible with MyBB 1.6 forums. 
	 
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
