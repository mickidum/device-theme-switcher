<?php
	/**
	 * Set up environment for Device Theme Switcher's tests suite.
	 */

	/**
	 * The path to the WordPress tests checkout.
	 */
	if ( getenv('WP_TESTS_DIR') ) {
		define( 'WP_TESTS_DIR', getenv('WP_TESTS_DIR') );
	} else {
		define( 'WP_TESTS_DIR', '/tmp/wordpress-tests-lib' );
	}

	/**
	 * The path to the main file of the plugin to test.
	 */
	define( 'DEVICE_THEME_SWITCHER', dirname( __FILE__ ) . '/../dts_controller.php' );

	/**
	 * The WordPress tests functions.
	 *
	 * We are loading this so that we can add our tests filter
	 * to load the plugin, using tests_add_filter().
	 */
	require_once WP_TESTS_DIR . '/includes/functions.php';

	/**
	 * Manually load the plugin main file.
	 *
	 * The plugin won't be activated within the test WP environment,
	 * that's why we need to load it manually.
	 *
	 * You will also need to perform any installation necessary after
	 * loading your plugin, since it won't be installed.
	 */
	function _manually_load_plugin() {

	    require DEVICE_THEME_SWITCHER;

	    // Make sure plugin is installed here ...
	}
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	/**
	 * Sets up the WordPress test environment.
	 *
	 * We've got our action set up, so we can load this now,
	 * and viola, the tests begin.
	 */
	require WP_TESTS_DIR . '/includes/bootstrap.php';


	// EOF