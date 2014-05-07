<?php
	/** 
		Plugin Name: Device Theme Switcher
		Plugin URI: https://github.com/jamesmehorter/device-theme-switcher/
		Description: Set a separate themes for handheld and tablet devices under Appearance > Device Themes
		Version: 2.4
		Author: James Mehorter | jamesmehorter@gmail.com
		Author URI: http://www.jamesmehorter.com
		License: GPLV2
		License URI: http://www.gnu.org/licenses/gpl-2.0.html
		
		Copyright 2014  James mehorter  (email : jamesmehorter@gmail.com)
	
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
	/**
	 *	
	 * Load the plugin core routines
	 *
	 * This is where Device Theme Switcher hooks into the WordPress
	 * activation, deactivation, unintiall, init, and plugin_action_links
	 */
	include('inc/class.core.php');
	//Activation: Install any initial settings
	register_activation_hook(__FILE__, array('DTS_Core', 'activate'));
	//Deactivation: Run any special routines on deactivation
	register_deactivation_hook(__FILE__, array('DTS_Core', 'deactivate'));
	//Uninstall: Remove anything stored in the database
	register_uninstall_hook(__FILE__, array('DTS_Core', 'uninstall'));
	//Run any update actions (typically only the first time the plugin is updated)
	add_action('init', array('DTS_Core', 'init'));
	//Display a 'Settings' link with the plugin in the plugins list
    add_filter('plugin_action_links', array('DTS_Core', 'device_theme_switcher_settings_link'), 10, 2);

	/**
	 * Load the plugin update routines
	 *
	 * The update class checks the currently installed version and runs
	 * any necessary update routines. This only occurs once per version,
	 * and ONLY in the admin.
	 */
	if (is_admin()) : 
		//if (current_user_can('manage_options')) : 
			include('inc/class.update.php');
			//Run any update actions (typically only the first time the plugin is updated)
			add_action('admin_init', array('DTS_Update', 'init'));
			add_action('admin_notices', array('DTS_Update', 'update_notice'));
		//endif;
	endif;

	/**
	 * Load the plugin admin features
	 *
	 * The admin features include the display of the status output in the Dashboard 
	 * 'Right Now' widget. They also create an admin page at Appearance > Device Themes
	 * for the website admin to save the plugin settings 
	 */
	include('inc/class.wp-admin.php');
	//Add a notice about the selected device themes in the Dashboard Right Now widget
	add_action('activity_box_end', array('DTS_Admin', 'right_now'));
	//Create our plugin admin page under the 'Appearance' menu
	add_action('admin_menu', array('DTS_Admin', 'admin_menu'));
	//Check if we need to save any form data that was submitted
	add_action('load-appearance_page_device-themes', array('DTS_Admin', 'load'));

	/**
	 * Load the plugin theme switching functionality
	 *
	 * The theme switching utilizes the MobileESP library to detect
	 * the browser User Agent and determine if it's a 'handheld' or 'tablet'.
	 * This plugin then taps into the WordPress template and stylesheet hooks 
	 * to deliver the alternately set themes in Appearance > Device Themes
	 */
    if ( is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX) ) :
        //We only want to tap into the theme filters if a frontend page or an ajax request is being requested
    else :
		//Include our external device theme switcher class library
		include('inc/class.switcher.php');
		//Load support for legacy GET variables
		include('inc/legacy/legacy_get_support.php');
		//Instantiate a new instance of this class
		//This is the single class instance that is accessible via 'global $dts;'
		$dts = new DTS_Switcher ;
		//Hook into the template output function with a filter and change the template delivered if need be
		add_filter('template', array($dts, 'deliver_template'), 10, 0);
		//Hook into the stylesheet output function with a filter and change the stylesheet delivered if need be
		add_filter('stylesheet', array($dts, 'deliver_stylesheet'), 10, 0);
		//Include the template tags developers can access in their themes
		include('inc/inc.template-tags.php');
		//Load support for legacy classes, methods, functions, and variables
		include('inc/legacy/legacy_structural_support.php');
	endif;

	/**
	 * Load in the plugin widgets
	 *
	 * The widgets create an option for capable users to place 'View Full Website'
	 * and 'Return to Mobile Website' links in their theme sidebars.
	 */
	//Include our external widget class library
	include('inc/class.widgets.php');
	//Register our widgets for displaying a 'View Full Website' and 'Return to mobile website' links
	function dts_register_widgets () {
		//Register the 'View Full Website' widget
		register_widget('DTS_View_Full_Website');
		//Register the 'Return to Mobile Website' widget
		register_widget('DTS_Return_To_Mobile_Website');
	}//END FUNCTION dts_register_widgets
	add_action( 'widgets_init', 'dts_register_widgets' );

	/**
	 * Load the plugin shortcodes
	 *
	 * The shortcodes allow capable users to place 'View Full Website' and
	 * 'Return to Mobile Website' links in their posts / pages.
	 */
	//Include our external shortcodes class library
	include('inc/class.shortcodes.php');
	//Register the [device-theme-switcher] shortcode
	//Ex: [link_to_full_website link_text="View Full Website" css_classes="blue-text, alignleft"]
	//Ex: [link_back_to_device link_text="Return to Mobile Website" css_classes="blue-text, alignleft"]
	//This shortcode outputs an HTML <a> link for the user to 'View Full Website' or to 'Return to Mobile Website'
	$dts_shortcode = new DTS_Shortcode() ;
	add_shortcode( 'link_to_full_website', array($dts_shortcode, 'link_to_full_website_shortcode') );
	add_shortcode( 'link_back_to_device', array($dts_shortcode, 'link_back_to_device_shortcode') );