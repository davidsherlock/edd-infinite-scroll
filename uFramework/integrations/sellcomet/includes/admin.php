<?php
/**
 * Scripts
 *
 * @package     uFramework\Sell_Comet\Admin
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'sellcomet_admin_menu' ) ) {
    add_action('admin_menu', 'sellcomet_admin_menu');
    function sellcomet_admin_menu() {
        add_menu_page( 'Sell Comet Dashboard', 'Sell Comet', 'manage_options', 'sellcomet', 'sellcomet_admin_page', 'dashicons-cart', 50 );
    }
}

if ( ! function_exists( 'sellcomet_admin_page' ) ) {
    function sellcomet_admin_page() {
        ?>
        <div class="wrap">
            <h1>Sell Comet Dashboard</h1>

            <div id="sellcomet-welcome-panel" class="welcome-panel">
                <div class="welcome-panel-content">

                    <a href="https://sellcomet.com" target="_blank">
                        <?php echo sellcomet_product_logo(); ?>
                    </a>

                    <h2>Welcome to the Sell Comet dashboard!</h2>
                    <p class="about-description">We have assembled a collection of useful links to help get you started:</p>

                    <div class="welcome-panel-column-container">

                        <div class="welcome-panel-column">
                            <h3>More Plugins</h3>
                            <ul>
                                <li><a href="https://profiles.wordpress.org/sellcomet" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-wordpress"></i> Sell Comet on WordPress.org</a></li>
                                <li><a href="<?php echo sellcomet_url(); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-admin-site"></i> Sell Comet Website</a></li>
                            </ul>
                        </div>

                        <div class="welcome-panel-column">
                            <h3>Contact Us</h3>
                            <ul>
                                <li><a href="mailto:support@sellcomet.com" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-email-alt"></i> support@sellcomet.com</a></li>
                                <li><a href="<?php echo sellcomet_support_url(); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-sos"></i> Customer Support</a></li>
                            </ul>
                        </div>

                        <div class="welcome-panel-column welcome-panel-last">
                            <h3>Follow Us</h3>
                            <ul class="sellcomet-social">
                                <li><a href="https://twitter.com/davidsherlockio" target="_blank" class="social-twitter"><i class="dashicons dashicons-twitter"></i> @davidsherlockio</a></li>
                                <li><a href="https://www.facebook.com/sellcomet/" target="_blank" class="social-facebook"><i class="dashicons dashicons-facebook-alt"></i> Facebook</a></li>
                            </ul>
                        </div>

                    </div>

                </div>
            </div>

            <div class="sellcomet-plugins">
                <?php

                $active_plugins = (array) get_option( 'active_plugins', array() );

                foreach ( get_plugins() as $plugin_path => $plugin ) : ?>
                    <?php if ( $plugin['Author'] == 'Sell Comet' && $plugin['AuthorURI'] == 'https://sellcomet.com' && in_array( $plugin_path, $active_plugins ) ) : ?>
                        <?php $from_wordpress = ( strpos( $plugin["PluginURI"], 'wordpress.org' ) !== false ); ?>
                        <?php $plugin_name = $plugin['TextDomain']; ?>
                        <?php
                        // Filterable values
                        $has_premium_version = apply_filters( "sellcomet_{$plugin_name}_has_premium_version", false );
                        $settings = apply_filters( "sellcomet_{$plugin_name}_settings", false );
                        ?>

                        <div class="sellcomet-plugin <?php echo ( ( $from_wordpress ) ? 'from-wordpress' : '' ); ?> ">
                            <div class="postbox">
                                <h2>
                                    <span><?php echo $plugin['Title']; ?></span>
                                    <small><?php echo $plugin['Version']; ?></small>
                                </h2>
                                <div class="inside">
                                    <p><?php echo $plugin["Description"]; ?></p>

                                    <?php if ( ! $from_wordpress ) : ?>

                                        <?php
                                        $license = uframework_get_option( $plugin_name . '-license', 'key', '' );
                                        $details  = uframework_get_option( $plugin_name . '-license', 'details' );
                                        $active  = ( is_object( $details ) &&  $details->license === 'valid' ) ? true : false;
                                        $action = 'activate';

                                        if ( $active ) {
                                            $action = 'deactivate';
                                            $license = substr_replace( $license, str_repeat( '*', strlen( $license ) - 8 ), 4, -4 );
                                        }
                                        ?>

                                        <form method="post" action="" class="sellcomet-license-form">

                                            <?php wp_nonce_field( $plugin_name . '-license-nonce', $plugin_name . '-license-nonce' ); ?>

                                            <input type="text" id="<?php echo $plugin_name; ?>-license-key" name="<?php echo $plugin_name; ?>-license-key" value="<?php echo $license; ?>" class="regular-text" placeholder="License key" <?php if ( $active ) : ?>readonly<?php endif; ?>>

                                            <input type="hidden" name="action" value="sellcomet_<?php echo $action; ?>_license">
                                            <input type="submit" id="<?php echo $plugin_name . '-' . $action; ?>" name="<?php echo $plugin_name . '-' . $action; ?>" value="<?php echo ucfirst($action); ?> License" class="button-primary">
                                            <span class="spinner"></span>
                                        </form>
                                    <?php elseif ( $has_premium_version ) : ?>
                                        <a href="<?php echo sellcomet_product_url( $plugin_name . '-pro' ); ?>" target="_blank" class="button-primary"> Get support, updates and pro features</a>
                                    <?php endif; ?>
                                </div>
                                <div class="actions">
                                    <a href="<?php echo $plugin["PluginURI"]; ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-<?php if ( $from_wordpress ) : ?>wordpress<?php else : ?>admin-site<?php endif; ?>"></i> Plugin Site</a>

                                    <?php if ( ! $from_wordpress || $has_premium_version ) : ?>
                                        <a href="<?php echo sellcomet_product_docs_url( $plugin_name ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-media-text"></i> Documentation</a>
                                    <?php endif; ?>

                                    <?php if ( ! $from_wordpress ) : ?>
                                        <a href="<?php echo sellcomet_support_url(); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-sos"></i> Support</a>
                                    <?php endif; ?>

                                    <?php if ( $settings !== false ) : ?>
                                        <a href="<?php echo $settings; ?>" class="uframework-icon-link"><i class="dashicons dashicons-admin-generic"></i> Settings</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
