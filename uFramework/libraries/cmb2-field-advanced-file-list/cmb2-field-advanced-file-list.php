<?php
/**
 * @package      CMB2\Field_Advanced_File_List
 * @author       Sell Comet
 * @copyright    Copyright (c) Sell Comet
 *
 * Plugin Name: CMB2 Field Type: Advanced File List
 * Plugin URI: https://github.com/rubengc/cmb2-field-advanced-file-list
 * GitHub Plugin URI: https://github.com/rubengc/cmb2-field-advanced-file-list
 * Description: CMB2 field type to attach posts, users or terms.
 * Version: 1.0.1
 * Author: Sell Comet
 * Author URI: https://sellcomet.com/
 * License: GPLv2+
 */

if ( ! class_exists( 'CMB2_Field_Advanced_File_List' ) ) {

    /**
     * Class CMB2_Field_Advanced_File_List
     */
    class CMB2_Field_Advanced_File_List {

        /**
         * Current version number
         */
        const VERSION = '1.0.0';

        /**
         * Initialize the plugin by hooking into CMB2
         */
        public function __construct() {
            add_filter( 'cmb2_admin_init', array( $this, 'includes' ) );

            add_filter( 'cmb2_render_class_advanced_file_list', array( $this, 'render_class' ), 10, 2 );

            add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
        }

        public function includes() {
            require_once __DIR__ . '/CMB2_Type_Advanced_File_List.php';
        }

        public function render_class( $render_class_name, $field_type_object ) {
            return 'CMB2_Type_Advanced_File_List';
        }

        /**
         * Enqueue scripts and styles
         */
        public function setup_admin_scripts() {

            wp_register_script( 'cmb-advanced-file-list-js', plugins_url( 'js/advanced-file-list.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
            wp_enqueue_script( 'cmb-advanced-file-list-js' );

            wp_enqueue_style( 'cmb-advanced-file-list-css', plugins_url( 'css/advanced-file-list.css', __FILE__ ), array(), self::VERSION );

        }

    }

    $cmb2_field_advanced_file_list = new CMB2_Field_Advanced_File_List();

}
