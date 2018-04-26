<?php
/**
 * EDD Infinite Scroll Functions
 *
 * This class provides the core functionality for the Infinite Scroll plugin
 *
 * @package     EDD\Infinite_Scroll\Classes\Functions
 * @copyright   Copyright (c) 2018, Sell Comet
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Infinite_Scroll_Functions' ) ) {

    /**
     * EDD Infinite Scroll Functions Class
     *
     * @since       1.0.0
     */
    class EDD_Infinite_Scroll_Functions {

        /**
         * Get things going
         *
         * @since       1.0.0
         */
        public function __construct() {
            // Filter Easy Digital Downloads [downloads] parameters/attributes
            add_filter( 'shortcode_atts_downloads', array( $this, 'shortcode_atts_downloads' ), 10, 4 );

            // Easy Digital Downloads [downloads] shortcode wrapper classes
            add_filter( 'edd_downloads_list_wrapper_class', array( $this, 'edd_downloads_list_wrapper_class' ), 10, 2 );

            // Filter Easy Digital Downloads [downloads] shortcode via Ajax
            add_filter( 'downloads_shortcode', array( $this, 'downloads_shortcode' ), 10, 2 );

            // WordPress Public Ajax requests
            add_action( 'wp_ajax_edd_infinite_scrolling', array( $this, 'infinite_scroll' ) );

            // WordPress Private Ajax requests
            add_action( 'wp_ajax_nopriv_edd_infinite_scrolling', array( $this, 'infinite_scroll' ) );
        }

        /**
         * [downloads] custom attributes
         *
         * @since       1.0.0
         */
        public function shortcode_atts_downloads( $out, $pairs, $atts, $shortcode ) {

            // Default custom attributes
            $custom_pairs = array(
                'infinite_scroll' => (bool) edd_infinite_scroll()->options->get( 'enabled_by_default', false ) ? 'yes' : 'no',
            );

            foreach ( $custom_pairs as $name => $default ) {
                if ( array_key_exists( $name, $atts ) )
                    $out[ $name ] = $atts[ $name ];
                else
                    $out[ $name ] = $default;
            }

            // Set pagination to false if infinite scrolling is enabled
            if ( $out['infinite_scroll'] == 'yes' ) {
                $out['pagination'] = 'false';
            }

            return $out;
        }

        /**
         * [downloads] wrapper classes
         *
         * @since       1.0.0
         */
        public function edd_downloads_list_wrapper_class( $wrapper_class, $atts ) {
            if ( $atts['infinite_scroll'] == 'yes' ) {
                $wrapper_class .= ' edd-infinite-scrolling';
            }

            return $wrapper_class;
        }

        /**
         * Creates a hidden form with shortcode atts to pass through to ajax
         *
         * @since       1.0.0
         */
        public function downloads_shortcode( $display, $atts ) {
            if( $atts['infinite_scroll'] == 'yes' && ! defined( 'DOING_EDD_INFINITE_SCROLL_AJAX' ) ) {
                    if ( get_query_var('paged') )
                        $paged = get_query_var('paged');
                    else if (get_query_var('page'))
                        $paged = get_query_var('page');
                    else
                        $paged = 1;

                    ob_start(); ?>
                    <form id="edd-infinite-scrolling-shortcode-atts" action="">
                        <?php foreach ( $atts as $key => $value ) : ?>
                            <?php if ( ! empty( $value ) ) : ?>
                                <input type="hidden" name="shortcode_atts[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <input type="hidden" name="paged" value="<?php echo $paged; ?>">
                    </form>
                    <?php $shortcode_atts_form = ob_get_clean();

                    $display = $shortcode_atts_form . $display;

                // Infinite scrolling loader
                $display .= '<div class="edd-infinite-scrolling-loader"><span class="edd-loading" aria-label="Loading"></span></div>';
            }

            return $display;
        }

        /**
         * WordPress Ajax requests
         *
         * @since  1.0.0
         */
        public function infinite_scroll() {
            if ( ! isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( $_REQUEST['nonce'], 'edd_infinite_scroll_nonce' ) ) {
                wp_send_json_error( 'invalid_nonce' );
                wp_die();
            }

            // Global to check if current ajax request comes from here
            define( 'DOING_EDD_INFINITE_SCROLL_AJAX', true );

            // Shortcode attributes
            $shortcode_atts = $_REQUEST['shortcode_atts'];

            // Set current page
            set_query_var( 'paged', isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1 );

            $response = array();

            // The content to return is the returned from the shortcode [downloads]
            $response['html'] = do_shortcode( '[downloads ' .
                implode(' ',
                    array_map( function( $key, $value ) {
                        return $key . '="' . $value . '"';
                    }, array_keys( $shortcode_atts ), $shortcode_atts )
                ) .
                ']' );

            // If [downloads] returns "No Downloads found" then is the end
            $response['is_end'] = strpos( $response['html'], sprintf( _x( 'No %s found', 'download post type name', 'easy-digital-downloads' ), edd_get_label_plural() ) ) !== false;

            // If [downloads] returns "No Downloads found" on a page different than 1, then is the end of the list and it returns emoty html
            if ( $response['is_end']
                && get_query_var('paged') > 1 ) {

                $response['html'] = '';
            }

            wp_send_json( $response );
            wp_die();
        }

    }

}
