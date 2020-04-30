<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://provenotif.com
 * @since      1.0.0
 *
 * @package    Prove_Notif
 * @subpackage Prove_Notif/admin
 */

class Prove_Notif_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }


    /**
     * Add link setting into list plugins page
     */
    public function add_plugin_links( $links ) {
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=provenotif' ) . '">' . __( 'Settings', 'prove-notif' ) . '</a>',
        );
        return array_merge( $plugin_links, $links );
    }


    /**
     * Add menu setting on Admin Dashboard
     */
    public function admin_menu() {
        $page_title = esc_html__( 'ProveNotif Plugin', 'weddingpress' );
        $menu_title = esc_html__( 'ProveNotif', 'weddingpress' );
        $capability = 'manage_options';
        $slug = 'provenotif';
        $callback = array( $this, 'plugin_page' );
        $icon_url = plugins_url( 'provenotif/assets/img/icon.png' );
        $position = 36;
        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon_url, $position );
    }


    /**
     * Add section settings on admin
     */
    public function admin_section() {
        add_settings_section( 'provenotif_license_section', esc_html__( '', 'prove-notif' ), array(), 'provenotif' );
        if ( $this->is_active() ) {
            add_settings_section( 'provenotif_support_section', esc_html__( '', 'provenotif' ), array(), 'provenotif' );
            
        }
    }


    /**
     * Display wrapper for form settings
     */
    public function plugin_page() { 
        echo '<div class="wrap">';
        echo '<h2>'.esc_html__( 'ProveNotif License', 'prove-notif' ).'</h2>';
        echo '<form method="POST" action="options.php">';
            settings_fields( 'provenotif' );
            do_settings_sections( 'provenotif' );
        echo '</form>';
        echo '</div>';
    }


    public function setup_fields() {
        $fields = array(
            array(
                'label' => esc_html__( 'License Key', 'prove-notif' ),
                'id' => 'provenotif_license',
                'type' => 'license',
                'section' => 'provenotif_license_section',
            )
        );
        $fields[] = array(
            'label' => esc_html__( 'Support', 'provenotif' ),
            'id' => 'provenotif_support',
            'type' => 'note',
            'section' => 'provenotif_support_section',
            'label2' => 'Layanan support, lapor bug dan feedback, silahkan klik <a href="https://provenotif.com/support/" target="_blank">support</a>',
        );
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
            'class' => '',
        );
        $field = wp_parse_args( $field, $defaults );
        switch ( $field['type'] ) {
            case 'license':
                $this->license_field();
                break;
            case 'note':
                printf( '<label for="%1$s">%2$s</label><br/>',
                    $field['id'],
                    $field['label2']
                );
                break;
            case 'checkbox':
                printf( '<label for="%1$s"><input name="%1$s" id="%1$s" class="%2$s" type="%3$s" value="%4$s" %5$s /> %6$s</label><br/>',
                    $field['id'],
                    $field['class'],
                    $field['type'],
                    'yes',
                    checked( $value, 'yes', false ),
                    $field['label2']
                );
                break;
            case 'radio':
                if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $field['options'] as $key => $label ) {
                        $iterator++;
                        $options_markup.= sprintf('<label for="%1$s_%7$s"><input name="%1$s" id="%1$s_%7$s" class="%2$s" type="%3$s" value="%4$s" %5$s /> %6$s</label><br/>',
                            $field['id'],
                            $field['class'],
                            $field['type'],
                            $key,
                            checked($value, $key, false),
                            $label,
                            $iterator
                        );
                    }
                    printf( '<fieldset>%s</fieldset>',
                        $options_markup
                    );
                }
                break;
            case 'select':
                if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
                    $attr = '';
                    $options = '';
                    foreach( $field['options'] as $key => $label ) {
                        $options.= sprintf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($value, $key, false),
                            $label
                        );
                    }
                    printf( '<select name="%1$s" id="%1$s" class="%2$s" %3$s>%4$s</select>',
                        $field['id'],
                        $field['class'],
                        $attr,
                        $options
                    );
                }
                break;
            case 'multiselect':
                if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
                    $attr = '';
                    $options = '';
                    foreach( $field['options'] as $key => $label ) {
                        $options.= sprintf('<option value="%s" %s>%s</option>',
                            $key,
                            !empty( $value) ? selected($value[array_search($key, $value, true)], $key, false) : false,
                            $label
                        );
                    }
                    $attr = ' multiple="multiple" ';
                    printf( '<select name="%1$s[]" id="%1$s" class="%2$s" %3$s>%4$s</select>',
                        $field['id'],
                        $field['class'],
                        $attr,
                        $options
                    );
                }
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" class="%2$s" placeholder="%3$s" rows="5" cols="50">%4$s</textarea>',
                    $field['id'],
                    $field['class'],
                    $field['placeholder'],
                    $value
                );
                break;
            case 'wysiwyg':
                wp_editor($value, $field['id']);
                break;
            case 'number':
                printf( '<input name="%1$s" id="%1$s" class="%2$s" type="%3$s" placeholder="%4$s" value="%5$s" min="0" />',
                    $field['id'],
                    $field['class'],
                    $field['type'],
                    $field['placeholder'],
                    $value
                );
                break;
            default:
                printf( '<input name="%1$s" id="%1$s" class="%2$s" type="%3$s" placeholder="%4$s" value="%5$s" />',
                    $field['id'],
                    $field['class'],
                    $field['type'],
                    $field['placeholder'],
                    $value
                );
        }
        if( $desc = $field['desc'] ) {
            printf( '<p class="description">%s </p>', $desc );
        }
    }


    /**
     * Display form license
     */
    public function license_field() {
        if ( ! PROVE_NOTIF_PLUGIN ) {
            return;
        }
        $license = trim( get_option( 'provenotif_license' ) );
        $this->check_license();
        $license_status = get_option( 'provenotif_license_status' );

        if ( isset( $license_status->license ) && ( $license_status->license == 'valid' || $license_status->license == 'expired' || $license_status->license == 'no_activations_left' ) ) :
            $expires = '';

            if ( isset( $license_status->expires ) && 'lifetime' != $license_status->expires ) {
                $expires = ', sampai '.date_i18n( get_option( 'date_format' ), strtotime( $license_status->expires, current_time( 'timestamp' ) ) );
            } elseif ( isset( $license_status->expires ) && 'lifetime' == $license_status->expires ) {
                $expires = ', lifetime';
            }

            $site_count = $license_status->site_count;
            $license_limit = $license_status->license_limit;
            if ( 0 == $license_limit ) {
                $license_limit = ', unlimited';
            } elseif ( $license_limit > 1 ) {
                $license_limit = ', sudah digunakan '.$site_count.' website dari limit licensi '.$license_limit.' website';
            }

            if ( $license_status->license == 'expired' ) {
                $renew_link = '<br/><a href="'.PROVE_NOTIF_STORE.'/checkout/?edd_license_key=' . $license . '&download_id=' . PROVE_NOTIF_ID.'" target="_blank">&rarr; klik di sini untuk perpanjang lisensi &larr;</a>';
            } ?>

            <style>
                .prove-notif-yes{ background: none; color: #27ae60; } 
                .prove-notif-error{ background: none; color: #a00; }
                span.description{ display: block; }
            </style>
            <input name="provenotif_license" type="hidden" value="<?php echo $license; ?>">
            <input name="provenotif_license_hidden" id="provenotif_license_hidden" type="text" style="min-width:300px;" value="<?php echo $this->get_hidden_license( $license ); ?>" class="" placeholder="" disabled> <input name="provenotif_deactivate" class="button" type="submit" value="Deactivate License"> <?php 

            if ( $license_status->license == 'valid' ) : ?>
                <span class="description prove-notif-yes">
                    <br/>
                    <?php echo '<strong>'.$license_status->license.'</strong>'.$expires.$license_limit; ?>
                </span> <?php 

            elseif ( $license_status->license == 'expired' ) : ?>
                
                <span class="description prove-notif-error">
                    <br/>
                    <?php echo '<strong>'.$license_status->license.'</strong>'.$expires.$license_limit; ?>
                </span> <?php 
                echo $renew_link;

            elseif ( $license_status->license == 'no_activations_left' ) : ?>

                <span class="description prove-notif-error"><br/><?php 
                    echo '<strong>lisensi habis</strong>'.$license_limit; ?>
                </span><?php 
            endif;
        else : ?>
            <input name="provenotif_license" id="provenotif_license" type="text" style="min-width:300px;" value="<?php echo $license; ?>" class="" placeholder=""> <input name="provenotif_activate" class="button" type="submit" value="Activate License">
            <span class="description"><?php 
                if ( $license && isset( $license_status->license ) ) : ?>
                    <br/><span class="prove-notif-error">
                        Status lisensi: <?php echo '<strong>'.$license_status->license.'</strong>'; ?>
                    </span><?php 
                endif;

                echo '<br/><br/>'.sprintf( __( 'Masukkan kode lisensi, kode lisensi bisa Anda dapatkan di %s My Account %s', 'prove-notif' ), '<a href="'.esc_url('https://pootar.me/account/').'" target="_blank"><strong>', '</strong></a>' ); ?>
            </span><?php 
        endif;
    }

    public function check_license() {
        global $wp_version;

        $license = trim( get_option( 'provenotif_license' ) );
        $api_params = array(
            'edd_action' => 'check_license',
            'license' => $license,
            'item_name' => urlencode( PROVE_NOTIF_NAME ),
            'url'       => home_url()
        );

        $response = wp_remote_post( PROVE_NOTIF_STORE, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        if ( is_wp_error( $response ) )
            return;

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        if( $license_data->license == 'inactive' || $license_data->license == 'site_inactive' ) {
            if ( $license_data->activations_left === 0 ) {
                $license_data->license = 'no_activations_left';
            } else {
                $this->activate_license();
            }
        } 

        update_option( 'provenotif_license_status', $license_data );
    }


    public function activate_license() {
        $license = trim( get_option( 'provenotif_license' ) );
        $api_params = array(
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_name'  => urlencode( PROVE_NOTIF_NAME ), 
            'url'        => home_url()
        );

        $response = wp_remote_post( PROVE_NOTIF_STORE, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
        if ( is_wp_error( $response ) )
            return;

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        if ( false !== $license_data->success ) {
            $this->check_license();
        }
    }


    public function deactivate_license() {
        $license = trim( get_option( 'provenotif_license' ) );
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $license,
            'item_name'  => urlencode( PROVE_NOTIF_NAME ),
            'url'        => home_url()
        );

        $response = wp_remote_post( PROVE_NOTIF_STORE, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
        if ( is_wp_error( $response ) )
            return;

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        if( $license_data->license == 'deactivated' ) {
            delete_option( 'provenotif_license_status' );
        }
    }


    public function added_license( $option_name, $option_value ) {
        if ( isset( $_POST['provenotif_activate'] ) ) {
            $this->activate_license();
        }

        if ( isset( $_POST['provenotif_deactivate'] ) ) {
            $this->deactivate_license();
            delete_option( 'provenotif_license' );
            delete_option( 'provenotif_license_status' );
        }
    }

    public function updated_license( $option_name, $old_value, $value ) {
        if ( isset( $_POST['provenotif_activate'] ) ) {
            $this->activate_license();
        }

        if ( isset( $_POST['provenotif_deactivate'] ) ) {
            $this->deactivate_license();
            delete_option( 'provenotif_license' );
            delete_option( 'provenotif_license_status' );
        }
    }


    private function get_hidden_license( $license ) {
        if ( !$license )
            return $license;

        $start = substr( $license, 0, 5 );
        $finish = substr( $license, -5 );

        $license = $start.'xxxxxxxxxxxxxxxxxxxx'.$finish;

        return $license;
    }


    public function is_active() {
        if ( PROVE_NOTIF_PLUGIN ) {
            $license_status = get_option( 'provenotif_license_status' );
            if ( ! ( isset( $license_status->license ) && $license_status->license == 'valid' ) )
                return false;
        }
        else {
            $license_status = get_option( get_template().'_license_key_status' );
            if ( $license_status != 'valid' )
                return false;
        }
        return true;
    }

    public function updater() {
        $license_key = trim( get_option( 'provenotif_license' ) );
        if ( ! $license_key ) {
            return;
        }
        $edd_updater = new Prove_Notif_Updater( PROVE_NOTIF_STORE, PROVE_NOTIF_PLUGIN_FILE, array(
                'version'   => PROVE_NOTIF_VERSION, 
                'license'   => $license_key, 
                'item_name' => PROVE_NOTIF_NAME, 
                'author'    => 'Ihsan Sahab', 
                'beta'      => false
            )
        );
    }

}
