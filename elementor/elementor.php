<?php

namespace ProveNotif;

use ProveNotif\Widgets\ProveNotif_Widget_SocialProof;

class ProveNotif_Elementor {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */

	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */

	private $version;
	private static $instance;
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct() {

		$this->plugin_name = 'provenotif';

		$this->version = PROVENOTIF_VERSION;

			add_action( 'elementor/init', array( $this, 'on_init' ) );
			add_action( 'elementor/elements/categories_registered', array( $this, 'on_categories_registered' ) );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'on_widgets_registered' ) );
			add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'after_enqueue_styles' ) );
			add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'after_enqueue_scripts' ) );

	}


	/**
	 * On Init
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */

	public function on_init() {

	}

	/**
	 * On Categories Registered
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */

	public function on_categories_registered( $elements_manager ) {

		$elements_manager->add_category(

			'provenotif',
			array(
				'title' => __( 'ProveNotif', 'provenotif' ),
				'icon' => 'font',
			)
		);
	}



	/**
	 * On Widgets Registered
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */

	public function on_widgets_registered() {

		$this->includes();
		$this->register_widget();

	}

	/**
	 * After Enqueue Styles
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function after_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, trailingslashit( PROVENOTIF_URL ) . 'assets/css/provenotif.css', array(), $this->version, 'all' );
	}

	/**
	 * After Register Script
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function after_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, trailingslashit( PROVENOTIF_URL ) . 'assets/js/provenotif.js', array(), $this->version, false );
	}

	/**
	 * Includes
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */

	private function includes() {

		require_once ( PROVENOTIF_PATH . 'elementor/widget-socialproof.php' );

	}


	/**
	 * Register Widget
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */

	private function register_widget() {

		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new ProveNotif_Widget_SocialProof() );
	}

}


ProveNotif_Elementor::get_instance();
