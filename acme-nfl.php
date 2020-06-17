<?php
/**
 * Plugin Name:     ACME NFL List
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A customizable list of NFL teams, pulled from an API endpoint.
 * Author:          Adem Hamidovic
 * Author URI:      YOUR SITE HERE
 * Text Domain:     acme-nfl
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Acme_Nfl
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'ACMENFL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Register the custom block
require_once( ACMENFL__PLUGIN_DIR . 'blocks/nfl-list.php' );
