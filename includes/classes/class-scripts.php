<?php
/**
 * EDD Infinite Scroll Scripts
 *
 * This class provides the scripts functionality
 *
 * @package     EDD\Infinite_Scroll\Classes\Scripts
 * @copyright   Copyright (c) 2018, Sell Comet
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Infinite_Scroll_Scripts' ) ) {

    /**
     * EDD Infinite Scroll Scripts Class
     *
     * @since       1.0.0
     */
    class EDD_Infinite_Scroll_Scripts {

        /**
         * Get things going
         *
         * @since       1.0.0
         */
        public function __construct() {

            // Register frontend scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

            // Enqueue frontend scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );
        }

        /**
         * Register scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function register_scripts() {
            // Use minified libraries if SCRIPT_DEBUG is turned off
            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            // Register Stylesheets
            wp_register_style( 'edd-infinite-scroll-animate-css', EDD_INFINITE_SCROLL_URL . 'assets/css/animate' . $suffix . '.css', array(), EDD_INFINITE_SCROLL_VER, 'all' );
            wp_register_style( 'edd-infinite-scroll-css', EDD_INFINITE_SCROLL_URL . 'assets/css/edd-infinite-scroll' . $suffix . '.css', array(), EDD_INFINITE_SCROLL_VER, 'all' );

            // Register Scripts
            wp_register_script( 'edd-infinite-scroll-js', EDD_INFINITE_SCROLL_URL . 'assets/js/edd-infinite-scroll' . $suffix . '.js', array( 'jquery' ), EDD_INFINITE_SCROLL_VER, true );
        }

        /**
         * Enqueue frontend scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function enqueue_scripts( $hook ) {

            // Localize scripts
            $script_parameters = array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'nonce'	                => wp_create_nonce( 'edd_infinite_scroll_nonce' ),
                'in_animation'          => edd_infinite_scroll()->options->get( 'infinite_scroll_in_animation', '' ),
            );

            wp_localize_script( 'edd-infinite-scroll-js', 'edd_infinite_scrolling', $script_parameters );

            // Enqueue Stylesheets
            wp_enqueue_style( 'edd-infinite-scroll-animate-css' );
            wp_enqueue_style( 'edd-infinite-scroll-css' );

            // Enqueue Scripts
            wp_enqueue_script( 'edd-infinite-scroll-js' );

        }

    }

}
