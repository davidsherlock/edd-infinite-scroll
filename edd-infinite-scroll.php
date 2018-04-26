<?php
/**
 * Plugin Name:     Easy Digital Downloads - Infinite Scroll
 * Plugin URI:      https://wordpress.org/plugins/edd-infinite-scroll/
 * Description:     Infinite scrolling product lists for Easy Digital Downloads.
 * Version:         1.0.5
 * Author:          Sell Comet
 * Author URI:      https://sellcomet.com
 * Text Domain:     edd-infinite-scroll
 *
 * @package         EDD\Infinite_Scroll
 * @author          Sell Comet
 * @copyright       Copyright (c) 2018, Sell Comet
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Infinite_Scroll' ) ) {

    /**
     * Main EDD_Infinite_Scroll class
     *
     * @since       1.0.0
     */
    class EDD_Infinite_Scroll {

        /**
         * @var         EDD_Infinite_Scroll $instance The one true EDD_Infinite_Scrolling
         * @since       1.0.0
         */
        private static $instance;

        /**
         * @var         EDD_Infinite_Scroll_Functions EDD Infinite Scrolling functions
         * @since       1.0.0
         */
        public $functions;

        /**
         * @var         EDD_Infinite_Scroll_Options EDD Infinite Scrolling options
         * @since       1.0.0
         */
        public $options;

        /**
         * @var         EDD_Infinite_Scroll_Scripts EDD Infinite Scrolling scripts
         * @since       1.0.0
         */
        public $scripts;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Infinite_Scroll
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Infinite_Scroll();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_INFINITE_SCROLL_VER', '1.0.5' );

            // Plugin path
            define( 'EDD_INFINITE_SCROLL_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_INFINITE_SCROLL_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include uFramework libraries
            require_once EDD_INFINITE_SCROLL_DIR . 'uFramework/uFramework.php';

            // Include functions class
            require_once EDD_INFINITE_SCROLL_DIR . 'includes/classes/class-functions.php';

            // Include options class
            require_once EDD_INFINITE_SCROLL_DIR . 'includes/classes/class-options.php';

            // Include scripts class
            require_once EDD_INFINITE_SCROLL_DIR . 'includes/classes/class-scripts.php';

            // Instantiate functions class
            $this->functions = new EDD_Infinite_Scroll_Functions();

            // Instantiate options class
            $this->options = new EDD_Infinite_Scroll_Options();

            // Instantiate scripts class
            $this->scripts = new EDD_Infinite_Scroll_Scripts();
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_INFINITE_SCROLL_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_infinite_scroll_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-infinite-scroll' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-infinite-scroll', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-infinite-scroll/' . $mofile;

            if ( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-infinite-scroll/ folder
                load_textdomain( 'edd-infinite-scroll', $mofile_global );
            } elseif ( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-infinite-scroll/languages/ folder
                load_textdomain( 'edd-infinite-scroll', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-infinite-scroll', false, $lang_dir );
            }
        }

        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {

            // Setup "Premium Version" filter
            add_filter( 'sellcomet_edd-infinite-scroll_has_premium_version', '__return_true' );
        }

    }
}

/**
 * The activation function
 *
 * @since       1.0.0
 * @return      void
 */
function edd_infinite_scroll_activation() {

    // Default option => value
    $options = array(
        'enabled_by_default'         => 'on',
    );

    $plugin_slug = 'edd-infinite-scroll';

    update_option( $plugin_slug, $options );

}
register_activation_hook( __FILE__, 'edd_infinite_scroll_activation' );

/**
 * The main function responsible for returning the one true EDD_Infinite_Scroll_Pro
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Infinite_Scroll The one true EDD_Infinite_Scroll
 */
function edd_infinite_scroll() {
    if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
        if ( ! class_exists( 'EDD_Infinite_Scroll_Activation' ) ) {
            require_once 'includes/classes/class-activation.php';
        }

        $activation = new EDD_Infinite_Scroll_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return EDD_Infinite_Scroll::instance();
    }
}
add_action( 'plugins_loaded', 'edd_infinite_scroll' );
