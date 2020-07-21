<?php
/**
 * Plugin Name: ProveNotif
 * Plugin URI: https://provenotif.com/
 * Description: Plugin untuk Landing Page Elementor, membantu meningkatkan konversi penjualan melalui social proof.
 * Version: 1.0.0
 * Author: ProveNotif
 * Author URI: https://provenotif.com/
 *
 */ 

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( !defined( 'PROVENOTIF_NAME' ) ) 
    { define( 'PROVENOTIF_NAME', 'ProveNotif' ); }
if ( !defined( 'PROVENOTIF_STORE' ) ) 
    { define( 'PROVENOTIF_STORE', 'https://my.provenotif.com' ); }
if ( !defined( 'PROVENOTIF_PATH' ) ) 
    { define( 'PROVENOTIF_PATH', plugin_dir_path( __FILE__ ) ); }
if ( !defined( 'PROVENOTIF_WEB' ) ) 
    { define( 'PROVENOTIF_WEB', plugin_dir_url( __FILE__ ) ); }
if ( !defined( 'PROVENOTIF_URL' ) ) 
    { define( 'PROVENOTIF_URL', plugins_url( '', __FILE__ ) ); }
if ( !defined( 'PROVENOTIF_VERSION' ) ) 
    { define( 'PROVENOTIF_VERSION', '1.0.0' ); }
if ( !defined( 'PROVENOTIF_PLUGIN' ) ) 
    { define( 'PROVENOTIF_PLUGIN', true ); }
define( 'PROVENOTIF_VERSION_REQUIRED', '1.7' );
define( 'PROVENOTIF_PHP_VERSION_REQUIRED', '5.4' );

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
    if ( ! version_compare( ELEMENTOR_VERSION, PROVENOTIF_VERSION_REQUIRED, '>=' ) ) {
        add_action( 'admin_notices', 'pn_fail_load_out_of_date' );
        add_action( 'admin_init', 'pn_deactivate' );
        return;
    }
    
    // Check for required PHP version
    if ( ! version_compare( PHP_VERSION, PROVENOTIF_PHP_VERSION_REQUIRED, '>=' ) ) {
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
    
    $message = __( 'ProveNotif requires Elementor version at least ' . PROVENOTIF_VERSION_REQUIRED . '. Please update Elementor to continue.', 'provenotif' );

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
    $message = __( 'ProveNotif requires PHP version ' . PROVENOTIF_PHP_VERSION_REQUIRED .'+ to work properly. The plugins is deactivated for now.', 'provenotif' );

    printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );

    if ( isset( $_GET['activate'] ) ) 
        unset( $_GET['activate'] );
}

/**
 * Debug function
 * @since 1.0.0
 * @return array
 */
if ( ! function_exists( '__debug' ) ) :

    function __debug()
    {
        $bt     = debug_backtrace();
        $caller = array_shift($bt); ?>
        <pre class='__debug'><?php
        print_r([
            "file"  => $caller["file"],
            "line"  => $caller["line"],
            "args"  => func_get_args()
        ]); ?>
        </pre>
        <?php
    }

endif;

register_activation_hook( __FILE__, 'provenotif_activation' );  
function provenotif_activation() {
    if (! wp_next_scheduled ( 'provenotif_check_license_event' )) {
        wp_schedule_event( time(), 'daily', 'provenotif_check_license_event' );
    }
}

register_deactivation_hook( __FILE__, 'provenotif_deactivation' ); 
function provenotif_deactivation() {
    wp_clear_scheduled_hook( 'provenotif_check_license_event' );
}

/**
 * Checks if license is valid and gets expire date.
 *
 * @since 1.0.0
 *
 * @return string $message License status message.
 */
function notif_check_license() {
    global $wp_version;
    $license = trim( get_option( 'provenotif_license' ) );
    $api_params = array(
        'license'   => $license,
        'string'    => home_url()
    );
    $response = wp_remote_get( add_query_arg( $api_params, PROVENOTIF_STORE.'/sejoli-validate-license' ), array( 'timeout' => 15, 'sslverify' => false ) );
    if ( is_wp_error( $response ) )
        return;
    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
    if( $license_data->valid ) {
        update_option( 'provenotif_license_valid', 1 );
    } else {
        update_option( 'provenotif_license_valid', 0 );
    }
}
add_action( 'provenotif_check_license_event', 'notif_check_license' );

function notif_is_active() {
    $license = trim( get_option( 'provenotif_license' ) );
    $license_status = get_option( 'provenotif_license_valid' );
    if ( $license && intval($license_status) === 1 ) :
        return true;
    endif;

    return false;
}



if( !class_exists( 'Provenotif_Plugin' ) ) {

    class Provenotif_Plugin {

        private static $instance;

        public static function get_instance() {
            return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
        }

        public function __construct() {
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );
            add_action( 'admin_menu', array( $this, 'create_settings' ), 101 );
            add_action( 'admin_init', array( $this, 'setup_sections' ) );
            add_action( 'admin_init', array( $this, 'setup_fields' ) );
            if ( notif_is_active() ) {
                add_action( 'plugins_loaded', array( $this, 'elementor' ) );
                add_action( 'init', array( $this, 'elementor_init' ) );
            }
            add_action( 'added_option', array( $this, 'added_license' ), 10, 2 );
            add_action( 'updated_option', array( $this, 'updated_license' ), 10, 3 );
        }
        
        public function add_plugin_links( $links ) {
            $plugin_links = array(
                '<a href="' . admin_url( 'admin.php?page=provenotif' ) . '">' . __( 'Settings', 'provenotif' ) . '</a>',
            );
            return array_merge( $plugin_links, $links );
        }
        
        public function create_settings() {
            $page_title = esc_html__( 'ProveNotif', 'provenotif' );
            $menu_title = esc_html__( 'ProveNotif', 'provenotif' );
            $capability = 'manage_options';
            $slug = 'provenotif';
            $callback = array( $this, 'settings_content' );
            $icon_url = plugins_url( 'provenotif/assets/img/icon.png' );
            $position = 32;
            add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon_url, $position );
        }
        
        public static function get_license_key() {
            return trim( get_option( 'provenotif_license' ) );
        }

        public static function set_license_key( $license_key ) {
            return update_option( 'provenotif_license', $license_key );
        }

        public function settings_content() { 
            echo '<div class="provenotif-notice-form">';
            echo '<h1>'.esc_html__( 'ProveNotif', 'provenotif' ).'</h1>';
            echo '<form method="POST" action="options.php">';
                settings_fields( 'provenotif' );
                do_settings_sections( 'provenotif' );
                submit_button();
            echo '</form>';
            echo '</div>';
        }

        public function setup_sections() {
            add_settings_section( 'provenotif_license', esc_html__( 'Aktivasi Lisensi', 'provenotif' ), array(), 'provenotif'            );      
        }

        public function status_active() {
            $fields = array(
                array(
                    'label' => esc_html__( 'Your License Key', 'provenotif' ),
                    'id' => 'provenotif_license',
                    'type' => 'text',
                    'section' => 'provenotif_license',
                )
            );
            
        }
        
        public function license_field() {
            $license = trim( get_option( 'provenotif_license' ) );
            notif_check_license();
            $license_status = get_option( 'provenotif_license_valid' );
            ?>
            <style>
                .provenotif-notice-form {
                 padding: 10px 20px;
                 background: #fff;
                 margin-top: 15px;
                 width: 580px;
                 border-radius: 2px; }
                .provenotif-notice-yes{ background: none; color: #008000; font-style: italic; } 
                 span.description{ display: block; }
            </style>
                <input name="provenotif_license" id="provenotif_license" type="text" style="min-width:350px;" value="<?php echo $this->get_hidden_license( $license ); ?>" class="" placeholder="masukkan kode licensi di sini">
                <span class="description">
                    <?php 
                    $status = 'Inactive';
                    if ( notif_is_active() ) :
                        $status = 'Active';
                    endif;
                    ?>
                    <br/>
                    <span class="provenotif-notice-yes">
                        Status: <?php echo '<strong>'.$status.'</strong>'; ?>
                    </span><br/><br/>
                <input name="provenotif_activate" class="button" type="submit" value="Activate License">
            <?php 
        }

        public function email_field() {
            $email = trim( get_option( 'provenotif_email' ) );
            ?>
            <input name="provenotif_email" id="provenotif_email" type="email" style="min-width:280px;" value="<?php echo $email; ?>" class="regular-text code" placeholder="masukkan user email di sini">
            <?php
        }

        public function password_field() {
            $password = trim( get_option( 'provenotif_password' ) );
            ?>
            <input name="provenotif_password" id="provenotif_password" type="password" style="min-width:280px;" value="<?php echo $password; ?>" class="regular-text code" placeholder="masukkan user password di sini">
            <?php
        }

        /**
         * Activates the license key.
         *
         * @since 1.0.0
         */
        public function activate_license() {
            $email = trim( get_option( 'provenotif_email' ) );
            $password = trim( get_option( 'provenotif_password' ) );
            $license = trim( get_option( 'provenotif_license' ) );
            $api_params = array(
                'user_email'    => $email,
                'user_pass'     => $password,
                'license'       => $license,
                'string'        => home_url()
            );
            $response = wp_remote_post( PROVENOTIF_STORE.'/sejoli-license', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
            if ( is_wp_error( $response ) )
                return;
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if( $license_data->valid ) {
                update_option( 'provenotif_license_valid', 1 );
            } else {
                update_option( 'provenotif_license_valid', 0 );
            }
        }


        public function added_license( $option_name, $option_value ) {
            if ( isset( $_POST['provenotif_activate'] ) ) {
                $this->activate_license();
            }
            if ( isset( $_POST['provenotif_deactivate'] ) ) {
                $this->deactivate_license();
                delete_option( 'provenotif_license' );
                delete_option( 'provenotif_license_valid' );
            }
        }

        public function updated_license( $option_name, $old_value, $value ) {
            if ( isset( $_POST['provenotif_activate'] ) ) {
                $this->activate_license();
            }
            if ( isset( $_POST['provenotif_deactivate'] ) ) {
                $this->deactivate_license();
                delete_option( 'provenotif_license' );
                delete_option( 'provenotif_license_valid' );
            }
        }
        
        public function status_field() {
            if ( notif_is_active() ) {
                printf( '<p>Alhamdulillah.. ProveNotif sudah bisa anda gunakan. Semoga bisnis Anda makin lancar berkah dan profit melimpah.', 'provenotif' );
                printf( '<p class="provenotif-notice-active"><a href="https://my.provenotif.com/member-area" target="_blank">My Account</a> | <a href="https://t.me/ProveNotifBot" target="_blank">Support via Telegram</a> | <a href="https://wa.me/6285725096235" target="_blank">Support via Whatsapp</a> | <a href="https://my.provenotif.com/tutorial" target="_blank">Tutorial</a>', 'provenotif' );
        
            }
            
        }

        /**
         * Hidden License Key
         * Credits: Agus Muhammad
         * https://agusmu.com
         * since 1.0.0
         */

        private function get_hidden_license( $license ) {
            if ( !$license )
                return $license;
            $start = substr( $license, 0, 5 );
            $finish = substr( $license, -5 );
            $license = $start.'xxxxxxxxxxxxxxxxxxxx'.$finish;
            return $license;
        }
        
        public function setup_fields() {
            $fields = array(
                array(
                    'label' => esc_html__( 'Your Email', 'provenotif' ),
                    'id' => 'provenotif_email',
                    'type' => 'email',
                    'section' => 'provenotif_license',
                ),
                array(
                    'label' => esc_html__( 'Your Password', 'provenotif' ),
                    'id' => 'provenotif_password',
                    'type' => 'password',
                    'section' => 'provenotif_license',
                ),
                array(
                    'label' => esc_html__( 'Your License Key', 'provenotif' ),
                    'id' => 'provenotif_license',
                    'type' => 'license',
                    'section' => 'provenotif_license',
                ),
            );
            if ( notif_is_active() ) {
                $fields[] = array(
                    'label' => esc_html__( '', 'provenotif' ),
                    'id' => 'provenotif_status',
                    'type' => 'status',
                    'section' => 'provenotif_license',
            );
            }
            
            foreach( $fields as $field ){
                add_settings_field( $field['id'], $field['label'], array( $this, 'field_callback' ), 'provenotif', $field['section'], $field );
                if ( 'note' != $field['type'] ) {
                    if ( false === strpos( $field['id'], '[' ) ) {
                        register_setting( 'provenotif', $field['id'] );
                    }
                    else {
                        $a = explode( '[', $field['id'] );
                        $b = trim( $a[0] );
                        register_setting( 'provenotif', $b );
                    }
                }
            }
        }
        
        public function elementor() {
            if ( did_action( 'elementor/loaded' ) ) {
                include_once( PROVENOTIF_PATH . '/elementor/elementor.php' );
            }
        }

        public function elementor_init() {
            if ( did_action( 'elementor/loaded' ) ) {
                include_once( PROVENOTIF_PATH . '/elementor/elementor.php' );
            }
        }

        public function field_callback( $field ) {
            if ( false === strpos( $field['id'], '[' ) ) {
                $value = get_option( $field['id'] );
            }
            else {
                $a = explode( '[', $field['id'] );
                $b = trim( $a[0] );
                $c = trim( str_replace( ']', '', $a[1] ) );
                $d = get_option( $b );
                $value = isset( $d[$c] ) ? $d[$c] : false;
            }
            $defaults = array(
                'label'         => '',
                'label2'        => '',
                'type'          => 'text',
                'desc'          => '',
                'placeholder'   => '',
                'class'         => '',
            );
            $field = wp_parse_args( $field, $defaults );
            $field['db'] = $field['id'];
            $field['id'] = str_replace( array( '[', ']' ), '_', $field['id'] );
            switch ( $field['type'] ) {
                case 'email':
                    $this->email_field();
                    break;
                case 'password':
                    $this->password_field();
                    break;
                case 'license':
                    $this->license_field();
                    break;
                case 'status':
                    $this->status_field();
                    break;
                case 'note':
                    printf( '<label for="%1$s">%2$s</label><br/>',
                        $field['id'],
                        $field['label2']
                    );
                    break;
                default:
                    printf( '<input name="%1$s" id="%2$s" class="%3$s" type="%4$s" placeholder="%5$s" value="%6$s" />',
                        $field['db'],
                        $field['id'],
                        $field['field_class'],
                        $field['type'],
                        $field['placeholder'],
                        $value
                    );
            }
            if( $desc = $field['desc'] ) {
                printf( '<p class="description">%s </p>', $desc );
            }
        }
        
    }

}


$license = trim( get_option( 'provenotif_license' ) );
$license_status = get_option( 'provenotif_license_valid' );
if ( $license && intval($license_status) === 1 ) :
require 'updater/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/ihsansahab/provenotif/',
    __FILE__,
    'provenotif'
);

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('40491605d292d5728ae446a4083196e04e40d1e1');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
endif;


/** 
 * Redirect activate plugin
 * Credits: Agus Muhammad
 * https://agusmu.com
 */

if ( PROVENOTIF_PLUGIN ) {
    add_action( 'plugins_loaded' , array( 'Provenotif_Plugin' , 'get_instance' ), 0 );
    function provenotif_plugin_activate() {
        add_option( 'provenotif_activation_redirect', true );
    }
    register_activation_hook( __FILE__ , 'provenotif_plugin_activate');

    function provenotif_plugin_redirect() {
    if ( get_option( 'provenotif_activation_redirect', false ) ) {
        delete_option( 'provenotif_activation_redirect' );
        if ( !isset( $_GET['activate-multi'] ) ) {
            wp_redirect("admin.php?page=provenotif");
            exit;
            }
        }
    }

    add_action( 'admin_init', 'provenotif_plugin_redirect' );
    
}

add_action('wp_head', 'provenotif_preview_elementor');
function provenotif_preview_elementor() {

    if ( isset( $_GET['elementor-preview'] ) && $_GET['elementor-preview'] ) {
        ?>
        <style>
            section.provenotif-wrap {
                display: block !important;
            }
        </style>
        <?php    
    }

}

