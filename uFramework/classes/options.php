<?php
/**
 * Options
 *
 * @package         uFramework\Options
 * @since           1.0.0
 *
 * @author          Sell Comet
 * @copyright       Copyright (c) Sell Comet
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'uFramework_Options' ) ) {

    class uFramework_Options {

        public $options_key;

        public function __construct() {
            add_action( 'cmb2_admin_init', array( $this, 'register_form' ) );
            add_action( 'cmb2_save_options-page_fields', array( $this, 'maybe_save' ) );
            add_action( 'deleted_option', array( $this, 'maybe_reset' ) );
        }

        public function register_form() {
            // Override
        }

        // Utility function to reduce options fields definition
        public function process_boxes( $boxes = array() ) {

            $processed_boxes = array();

            foreach ( $boxes as $meta_box_id => $meta_box ) {

                if ( isset( $meta_box['fields'] ) ) {

                    foreach ( $meta_box['fields'] as $field_id => $field ) {
                        if ( ! isset( $field['id'] ) ) {
                            $field['id'] = $field_id;
                        }

                        $meta_box['fields'][$field_id] = $field;
                    }

                }

                $meta_box['id'] = $meta_box_id;

                $meta_box['classes'] = 'uframework-form uframework-options-form';

                $meta_box['show_on'] = array(
                    'key'   => 'options-page',
                    'value' => array( $this->options_key ),
                );

                $meta_box['display_cb'] = false;
                $meta_box['admin_menu_hook'] = false;

                $cmb = new_cmb2_box( $meta_box );

                $cmb->object_type( 'options-page' );

                $processed_boxes[] = $cmb;
            }

            return $processed_boxes;

        }

        public function maybe_save() {
            if ( isset( $_REQUEST['submit-cmb'] ) && ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == $this->options_key ) ) {
                $this->save_form();

                do_action( 'uframework_save_options', $this );
            }
        }

        public function maybe_reset( $option ) {
            if ( $option === $this->options_key && ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == $this->options_key ) ) {
                $this->reset_form();

                do_action( 'uframework_reset_options', $this );
            }
        }

        public function save_form() {
            // Override
        }

        public function reset_form() {
            // Override
        }

        public function visibility_button( $field_args, $field ) {
            uframework_field_visibility_button( array(
                'show_text' => __( 'Show' ) . ' ' . strtolower( $field->args( 'name' ) ),
                'hide_text' => __( 'Hide' ) . ' ' . strtolower( $field->args( 'name' ) ),
            ) );
        }

        public function get( $key = '', $default = null ) {
            if ( function_exists( 'cmb2_get_option' ) ) {
                // Use cmb2_get_option as it passes through some key filters.
                return cmb2_get_option( $this->options_key, $key, $default );
            }

            // Fallback to get_option if CMB2 is not loaded yet.
            $opts = get_option( $this->options_key, $key, $default );

            $val = $default;

            if ( 'all' == $key ) {
                $val = $opts;
            } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
                $val = $opts[ $key ];
            }

            return $val;
        }

        public function update( $key = '', $value, $single = true ) {
            if ( function_exists( 'cmb2_update_option' ) ) {
                // Use cmb2_update_option as it passes through some key filters.
                return cmb2_update_option( $this->options_key, $key, $value, $single );
            }

            $opts = get_option( $this->options_key, array() );

            if ( ! is_array( $opts ) ) {
                $opts = array();
            }

            if ( ! $single ) {
                $opts[ $key ][] = $value;
            } else {
                $opts[ $key ] = $value;
            }

            return update_option( $this->options_key, $opts );
        }
    }

}
