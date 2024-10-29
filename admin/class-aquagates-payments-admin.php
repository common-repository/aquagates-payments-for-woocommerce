<?php

/**
 * The admin-specific functionality of the plugin.
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Aquagates_Payments_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $aquagates_payments    The ID of this plugin.
	 */
	private $aquagates_payments;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $aquagates_payments       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $aquagates_payments, $version ) {

		$this->aquagates_payments = $aquagates_payments;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aquagates_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aquagates_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->aquagates_payments, plugin_dir_url( __FILE__ ) . 'css/aquagates-payments-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aquagates_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aquagates_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->aquagates_payments, plugin_dir_url( __FILE__ ) . 'js/aquagates-payments-admin.js', array( 'jquery' ), $this->version, false );

	}

}
