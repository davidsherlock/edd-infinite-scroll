<?php
/**
 * Sell Comet
 *
 * @package         uFramework\Sell_Comet
 * @since           1.0.0
 *
 * @author          Sell Comet
 * @copyright       Copyright (c) Sell_Comet
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'SELLCOMET_LOADED' ) ) {
    define( 'SELLCOMET_VER', '1.0.0' );
    define( 'SELLCOMET_DIR', __DIR__ );
    define( 'SELLCOMET_URL', plugin_dir_url( __DIR__ ) . 'sellcomet/' );

    // Classes
    require_once __DIR__ . '/classes/class-license.php';

    // Includes
    require_once __DIR__ . '/includes/admin.php';
    require_once __DIR__ . '/includes/functions.php';
    require_once __DIR__ . '/includes/scripts.php';

    // Include plugin.php so we can use the get_plugin_data function
    if ( ! function_exists('get_plugin_data') ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    };

    define( 'SELLCOMET_LOADED', true );
}

sellcomet_license_plugin( __DIR__ . '/../../../' );
