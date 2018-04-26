<?php
/**
 * EDD Infinite Scroll Settings
 *
 * This class provides the options/settings functionality
 *
 * @package     EDD\Infinite_Scroll\Classes\Options
 * @copyright   Copyright (c) 2018, Sell Comet
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Infinite_Scroll_Options' ) ) {

    /**
     * EDD Infinite Scroll Options Class
     *
     * @since       1.0.0
     */
    class EDD_Infinite_Scroll_Options extends uFramework_Options {

        /**
         * Get things going
         *
         * @since       1.0.0
         */
        public function __construct() {
            $this->options_key = 'edd-infinite-scroll';

            add_filter( 'sellcomet_' . $this->options_key . '_settings', array( $this, 'register_settings_url' ) );

            parent::__construct();
        }

        /**
         * Add the options metabox to the array of metaboxes
         *
         * @since       1.0.0
         */
        public function register_settings_url( $url ) {
            return 'admin.php?page=' . $this->options_key;
        }

        /**
         * Reset form options
         *
         * @since       1.0.0
         */
        public function reset_form() {
            // Restores default options
            edd_infinite_scroll_activation();
        }

        /**
         * Add the options metabox to the array of metaboxes
         *
         * @since       1.0.0
         */
        public function register_form() {

            // Options page configuration
            $args = array(
                'key'               => $this->options_key,
                'title'             => __( 'EDD Infinite Scroll', 'edd-infinite-scroll' ),
                'topmenu'           => 'sellcomet',
                'cols'              => 2,
                'boxes'             => $this->boxes(),
                'tabs'              => $this->tabs(),
                'menuargs'          => array(
                    'menu_title'    => __( 'EDD Infinite Scroll', 'edd-infinite-scroll' ),
                ),
                'savetxt'           => __( 'Save Settings', 'edd-infinite-scroll' ),
                'resettxt'          => __( 'Reset Settings', 'edd-infinite-scroll' ),
                'admincss'          => '.' . $this->options_key . ' #side-sortables{padding-top: 0 !important;}' .
                    '.' . $this->options_key . '.cmo-options-page .columns-2 #postbox-container-1{margin-top: 0 !important;}' .
                    '.' . $this->options_key . '.cmo-options-page .nav-tab-wrapper{display: none;}'
            );

            // Create the options page
            new Cmb2_Metatabs_Options( $args );
        }

        /**
         * Setup form in settings page
         *
         * @return array
         */
        public function boxes() {

            // Holds all CMB2 box objects
            $boxes = array();

            // Default options to all boxes
            $show_on = array(
                'key'               => 'options-page',
                'value' =>          array( $this->options_key ),
            );

            // General options box
            $cmb = new_cmb2_box( array(
                'id'                => $this->options_key . '_general',
                'title'             => __( 'General Options', 'edd-infinite-scroll' ),
                'show_on'           => $show_on,
                'display_cb'        => false,
                'admin_menu_hook'   => false,
            ) );

            $cmb->add_field( array(
                'name'              => __( 'Enable By Default', 'edd-infinite-scroll' ),
                'desc'              => __( 'Enable infinite scroll by default on all [downloads] shortcodes (overwritable by infinite_scroll="no")', 'edd-infinite-scroll' ),
                'id'                => 'enabled_by_default',
                'type'              => 'checkbox',
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Infinite scroll animations options box
            $cmb = new_cmb2_box( array(
                'id'                => $this->options_key . '_infinite_scroll_animations',
                'title'             => __( 'Animations', 'edd-infinite-scroll' ),
                'show_on'           => $show_on,
                'display_cb'        => false,
                'admin_menu_hook'   => false,
            ) );

            $cmb->add_field( array(
                'name'              => __( 'Entrance Animation', 'edd-infinite-scroll' ),
                'desc'              => '',
                'id'                => 'infinite_scroll_in_animation',
                'type'              => 'animation',
                'preview'           => true,
                'groups'            => array( 'entrances' ),
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Submit box
            $cmb = new_cmb2_box( array(
                'id'                => $this->options_key . '-submit',
                'title'             => __( 'Save Changes', 'edd-infinite-scroll' ),
                'show_on'           => $show_on,
                'display_cb'        => false,
                'admin_menu_hook'   => false,
                'context'           => 'side',
            ) );

            $cmb->add_field( array(
                'name'              => '',
                'desc'              => '',
                'id'                => 'submit_box',
                'type'              => 'title',
                'render_row_cb'     => array( $this, 'submit_box' )
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Shortcode box
            $cmb = new_cmb2_box( array(
                'id'                => $this->options_key . '-shortcode',
                'title'             => __( 'Shortcode Generator', 'edd-infinite-scroll' ),
                'show_on'           => $show_on,
                'display_cb'        => false,
                'admin_menu_hook'   => false,
                'context'           => 'side',
            ) );

            $cmb->add_field( array(
                'name'              => '',
                'desc'              => __( 'From this options page you can configure the default parameters for EDD Infinite Scroll. Using the form bellow you can generate a custom shortcode to place on any page.', 'edd-infinite-scroll' ),
                'id'                => 'shortcode_generator',
                'type'              => 'title',
                'after'             => array( $this, 'shortcode_generator' ),
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            return $boxes;
        }

        /**
         * Setup tabs in settings page
         *
         * @return array
         */
        public function tabs() {
            $tabs = array();

            $tabs[] = array(
                'id'    => 'general',
                'title' => 'General',
                'desc'  => '',
                'boxes' => array(
                    $this->options_key . '_general',
                    $this->options_key . '_infinite_scroll_animations',
                ),
            );

            return $tabs;
        }

        /**
         * Submit box
         *
         * @param array      $field_args
         * @param CMB2_Field $field
         */
        public function submit_box( $field_args, $field ) {
            ?>
            <p>
                <a href="<?php echo sellcomet_product_docs_url( $this->options_key ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-media-text"></i> <?php _e( 'Documentation', 'edd-infinite-scroll' ); ?></a>
                <a href="<?php echo sellcomet_product_url( $this->options_key ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-cart"></i> <?php _e( 'Get support and pro features', 'edd-infinite-scroll' ); ?></a>
            </p>
            <div class="cmb2-actions">
                <input type="submit" name="reset-cmb" value="<?php _e( 'Reset Settings', 'edd-infinite-scroll' ); ?>" class="button">
                <input type="submit" name="submit-cmb" value="<?php _e( 'Save Settings', 'edd-infinite-scroll' ); ?>" class="button-primary">
            </div>
            <?php
        }

        /**
         * Shortcode generator
         *
         * @param array      $field_args
         * @param CMB2_Field $field
         */
        public function shortcode_generator( $field_args, $field ) {
            ?>
            <div id="edd-infinite-scroll-shortcode-form" class="uframework-shortcode-generator">
                <p>
                    <textarea type="text" id="edd-infinite-scroll-shortcode-input" data-shortcode="downloads" readonly="readonly">[downloads infinite_scroll="yes"]</textarea>
                </p>

                <input type="hidden" id="shortcode_form_infinite_scroll" data-shortcode-attr="infinite_scroll" value="yes">
            </div>
            <?php
        }

    }

}
