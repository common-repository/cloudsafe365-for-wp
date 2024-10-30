<?php
 /*
   Plugin Name: cloudsafe365_for_WP
   Plugin URI: http://www.CloudSafe365.com/
   Description: <strong>Online Back Up, Malware, Security and Content Protection in ONE Plugin.</strong> cloudsafe365 is a simple to use plugin that provide online back up, malware detection and security from both advanced hacking techniques and content theft / scraping. Download today and give your site extreme web protection. and stops scraping of site data,  protecting the site content from theft.
   Version: 1.47
   Author: CloudSafe365
   Author URI: http://www.cloudsafe365.com
   License: GPL2
  */
 /*  Copyright 2011  CloudSafe365.com)
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as
   published by the Free Software Foundation.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */


// Don't call the file directly
 if (!defined('ABSPATH'))
   exit;

          if (!defined('CS365'))
                    define('CS365', 'http://www.cloudsafe365.com');

          if (!defined('CS365P'))
                    define('CS365P', CS365 . '/p/');

 if (is_admin()) {
 require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/ajax/cloudsafe365_ajaxreturns_admin.php');
 wp_enqueue_script('jquery');
 add_action('init', 'thickbox_register');
 register_activation_hook(__FILE__, 'cloudsafe365_install');
 register_deactivation_hook(__FILE__, 'acloudsafe365_deactivate');
 register_uninstall_hook(__FILE__, 'cloudsafe_uninstall');
 require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/admin/cloudsafe365_admin.php');
 }
 else
   require_once(WP_PLUGIN_DIR . '/cloudsafe365-for-wp/lib/class.cs365_security.php');
?>