<?php
/**
 * Scripts
 *
 * @package     uFramework\Sell_Comet\Scripts
 * @since       1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register scripts
 *
 * @since       1.0.0
 * @return      void
 */
if ( ! function_exists( 'sellcomet_register_scripts' ) ) {
    add_action( 'admin_enqueue_scripts', 'sellcomet_register_scripts' );
    function sellcomet_register_scripts() {

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        // Stylesheets
        wp_register_style( 'sellcomet-css', SELLCOMET_URL . 'assets/css/sellcomet' . $suffix . '.css', array( ), '1.0.0', 'all' );

        // Scripts
        wp_register_script( 'sellcomet-js', SELLCOMET_URL . 'assets/js/sellcomet' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );

    }
}

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
if ( ! function_exists( 'sellcomet_admin_enqueue_scripts' ) ) {
    add_action( 'admin_enqueue_scripts', 'sellcomet_admin_enqueue_scripts', 100 );
    function sellcomet_admin_enqueue_scripts( $hook ) {
        //Stylesheets
        wp_enqueue_style( 'sellcomet-css' );

        // Localize scripts
        $script_parameters = array(
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'sellcomet_nonce' ),
        );

        wp_localize_script( 'sellcomet-js', 'sellcomet', $script_parameters );

        //Scripts
        wp_enqueue_script( 'sellcomet-js' );
    }
}
