<?php

/**
 * @wordpress-plugin
 * Plugin Name:       ProveNotif
 * Plugin URI:        https://provenotif.com
 * Description:       Plugin untuk Landing Page Elementor, membantu meningkatkan tingkat order melalui social proof.
 * Version:           1.0.0
 * Author:            Ihsan Sahab
 * Author URI:        https://provenotif.com
 * Text Domain:       prove-notif
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( !defined( 'PROVE_NOTIF_NAME' ) ) {
    define( 'PROVE_NOTIF_NAME', 'ProveNotif' );
}
if ( !defined( 'PROVE_NOTIF_ID' ) ) {
    define( 'PROVE_NOTIF_ID', 16 );
}
if ( !defined( 'PROVE_NOTIF_STORE' ) ) {
    define( 'PROVE_NOTIF_STORE', 'https://provenotif.com' );
}
if ( !defined( 'PROVE_NOTIF_PATH' ) ) {
    define( 'PROVE_NOTIF_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'PROVE_NOTIF_URL' ) ) {
    define( 'PROVE_NOTIF_URL', plugins_url( '', __FILE__ ) );
}
if ( !defined( 'PROVE_NOTIF_VERSION' ) ) {
    define( 'PROVE_NOTIF_VERSION', '1.0.0' );
}
if ( !defined( 'PROVE_NOTIF_PLUGIN' ) ) {
    define( 'PROVE_NOTIF_PLUGIN', true );
}
if ( !defined( 'PROVE_NOTIF_PLUGIN_FILE' ) ) {
    define( 'PROVE_NOTIF_PLUGIN_FILE', plugin_basename( __FILE__ ) );
}
if ( !defined( 'PROVE_NOTIF_PLUGIN_SLUG' ) ) {
    define( 'PROVE_NOTIF_PLUGIN_SLUG', plugin_basename( __FILE__ ) );
}
define( 'PROVE_NOTIF_VERSION_REQUIRED', '1.7' );
define( 'PROVE_NOTIF_PHP_VERSION_REQUIRED', '5.4' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/prove-notif.php';

/**
 * Check if Elementor is installed
 *
 * @since 1.0
 *
 */
if ( ! function_exists( '_is_elementor_installed' ) ) {
    function _is_elementor_installed() {
        $file_path = 'elementor/elementor.php';
        $installed_plugins = get_plugins();
        return isset( $installed_plugins[ $file_path ] );
    }
}

/**
 * Shows notice to user if Elementor plugin
 * is not installed or activated or both
 *
 * @since 1.0
 *
 */
function pn_fail_load() {
    $plugin = 'elementor/elementor.php';

    if ( _is_elementor_installed() ) {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
        $message = __( 'ProveNotif requires Elementor plugin to be active. Please activate Elementor to continue.', 'provenotif' );
        $button_text = __( 'Activate Elementor', 'provenotif' );

    } else {
        if ( ! current_user_can( 'install_plugins' ) ) {
            return;
        }

        $activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
        $message = sprintf( __( 'ProveNotif requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', 'provenotif' ), '<strong>', '</strong>' );
        $button_text = __( 'Install Elementor', 'provenotif' );
    }

    $button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
    
    printf( '<div class="error"><p>%1$s</p>%2$s</div>', esc_html( $message ), $button );
}

add_action( 'plugins_loaded', 'pn_init' );

function pn_init() {

    // Notice if the Elementor is not active
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'pn_fail_load' );
        return;
    }

    // Check for required Elementor version
    if ( ! version_compare( ELEMENTOR_VERSION, PROVE_NOTIF_VERSION_REQUIRED, '>=' ) ) {
        add_action( 'admin_notices', 'pn_fail_load_out_of_date' );
        add_action( 'admin_init', 'pn_deactivate' );
        return;
    }
    
    // Check for required PHP version
    if ( ! version_compare( PHP_VERSION, PROVE_NOTIF_PHP_VERSION_REQUIRED, '>=' ) ) {
        add_action( 'admin_notices', 'pn_fail_php' );
        add_action( 'admin_init', 'pn_deactivate' );
        return;
    }
    
}


/**
 * Shows notice to user if
 * Elementor version if outdated
 *
 * @since 1.0
 *
 */
function pn_fail_load_out_of_date() {
    if ( ! current_user_can( 'update_plugins' ) ) {
        return;
    }
    
    $message = __( 'ProveNotif requires Elementor version at least ' . PROVE_NOTIF_VERSION_REQUIRED . '. Please update Elementor to continue.', 'provenotif' );

    printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );
}

/**
 * Shows notice to user if minimum PHP
 * version requirement is not met
 *
 * @since 1.0
 *
 */
function pn_fail_php() {
    $message = __( 'ProveNotif requires PHP version ' . PROVE_NOTIF_PHP_VERSION_REQUIRED .'+ to work properly. The plugins is deactivated for now.', 'provenotif' );

    printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );

    if ( isset( $_GET['activate'] ) ) 
        unset( $_GET['activate'] );
}

/**
 * Deactivates the plugin
 *
 * @since 1.0
 */
function pn_deactivate() {
    deactivate_plugins( plugin_basename( __FILE__ ) );
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_provenotif() {

	$plugin = new ProveNotif();
	$plugin->run();

}
run_provenotif();