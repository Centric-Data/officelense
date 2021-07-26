<?php
/**
 * Uninstall Plugin
 *
 * @package     UninstallPlugin
 * @author      Centric Data
 * @copyright   2021 Centric Data
 * @license     GPL-2.0-or-later
 *
*/

// If uninstall.php is not called by Wordpress, die
if ( ! defined('WP_UNINSTALL_PLUGIN') ) {
    die();
}

$option_name = 'wporg_option';

delete_option( $option_name );

// For site options in Multisite
delete_site_option( $option_name );

// Drop a custom database table
global $wpdb;
$wpdb->query( 'DROP TABLE IF EXISTS {$wpdb->prefix}mytable' );
