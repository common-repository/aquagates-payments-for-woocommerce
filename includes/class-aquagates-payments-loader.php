<?php

/**
 * Register all actions and filters for the plugin
 *
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 */
class Aquagates_Payments_Loader{

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @access   protected
	 * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @access   protected
	 * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 */
	public function __construct(){

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param string $hook The name of the WordPress action that is being registered.
	 * @param object $component A reference to the instance of the object on which the action is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
	 * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ){
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param object $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
	 * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ){
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @access   private
	 * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param object $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority The priority at which the function should be fired.
	 * @param int $accepted_args The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ){

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 */
	public function run(){

		foreach ( $this->filters as $hook ){
			add_filter( $hook[ 'hook' ], array( $hook[ 'component' ], $hook[ 'callback' ] ), $hook[ 'priority' ], $hook[ 'accepted_args' ] );
		}

		foreach ( $this->actions as $hook ){
			add_action( $hook[ 'hook' ], array( $hook[ 'component' ], $hook[ 'callback' ] ), $hook[ 'priority' ], $hook[ 'accepted_args' ] );
		}

		add_action( 'admin_menu', array( $this, 'admin_menu_aquagates_payments' ), 55 );
		add_action( 'admin_init', array( $this, 'admin_init_aquagates_payments' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded_aquagates_payments' ), 20 );
	}

	/**
	 * Add to woocommerce admin menu
	 *
	 */
	public function admin_menu_aquagates_payments(){
		$page = add_submenu_page( 'woocommerce',
		                          'AquaGates Setting',
		                          'AquaGates Setting',
		                          'manage_woocommerce',
		                          'aquagates-payments-setting',
		                          array( $this, 'aquagates_payments_setting' ) );
	}

	/**
	 * Displaying the setting menu
	 *
	 */
	public function aquagates_payments_setting(){
		include( plugin_dir_path( __FILE__ ) . '../admin/partials/aquagates-payments-admin-display.php' );
	}

	/**
	 * Contents of the setting menu
	 *
	 */
	public function admin_init_aquagates_payments(){

		$option_group = 'aquagates_payments_options';
		register_setting( $option_group, $option_group . '_name' );

		$section = 'aquagates_payments_main';
		add_settings_section( $section, 'メイン設定', '', $option_group );

		add_settings_field(
			'aquagates_payments_main_client_id',
			'クライアントId（本番環境）',
			[ $this, 'aquagates_payments_main_client_id' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_client_id' );

		add_settings_field(
			'aquagates_payments_main_user_id',
			'ユーザーId（本番環境）',
			[ $this, 'aquagates_payments_main_user_id' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_user_id' );

		add_settings_field(
			'aquagates_payments_main_acstkn',
			'アクセストークン（本番環境）',
			[ $this, 'aquagates_payments_main_acstkn' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_acstkn' );

		add_settings_field(
			'aquagates_payments_main_test_mode_flg',
			'テスト動作モード',
			[ $this, 'aquagates_payments_main_test_mode_flg' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_test_mode_flg' );

		add_settings_field(
			'aquagates_payments_main_client_id_test',
			'クライアントId（テスト環境）',
			[ $this, 'aquagates_payments_main_client_id_test' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_client_id_test' );

		add_settings_field(
			'aquagates_payments_main_user_id_test',
			'ユーザーId（テスト環境）',
			[ $this, 'aquagates_payments_main_user_id_test' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_user_id_test' );

		add_settings_field(
			'aquagates_payments_main_acstkn_test',
			'アクセストークン（テスト環境）',
			[ $this, 'aquagates_payments_main_acstkn_test' ],
			$option_group,
			$section
		);
		$this->update_settings_field( 'aquagates_payments_main_acstkn_test' );
	}

	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_client_id(){
		$val  = get_option( 'aquagates_payments_main_client_id' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="text" id="input_aquagates_payments_main_client_id" name="aquagates_payments_main_client_id" value="' . esc_attr( $val ) . '" >';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'value' => [] ] ] );
	}


	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_acstkn(){
		$val  = get_option( 'aquagates_payments_main_acstkn' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="text" id="input_aquagates_payments_main_acstkn" name="aquagates_payments_main_acstkn" value="' . esc_attr( $val ) . '" >';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'value' => [] ] ] );
	}


	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_user_id(){
		$val  = get_option( 'aquagates_payments_main_user_id' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="text" id="input_aquagates_payments_main_user_id" name="aquagates_payments_main_user_id" value="' . esc_attr( $val ) . '" >';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'value' => [] ] ] );
	}

	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_test_mode_flg(){
		$val  = get_option( 'aquagates_payments_main_test_mode_flg' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="checkbox" id="input_aquagates_payments_main_test_mode_flg" name="aquagates_payments_main_test_mode_flg" ' . ( ( $val ) ? 'checked="checked"' : "" ) . '>';
		$html .= '  <label for="input_aquagates_payments_main_test_mode_flg">有効</label>';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [ 'for' => [] ], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'checked' => [] ] ] );
	}

	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_client_id_test(){
		$val  = get_option( 'aquagates_payments_main_client_id_test' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="text" id="input_aquagates_payments_main_client_id_test" name="aquagates_payments_main_client_id_test" value="' . esc_attr( $val ) . '" >';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'value' => [] ] ] );
	}

	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_acstkn_test(){
		$val  = get_option( 'aquagates_payments_main_acstkn_test' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="text" id="input_aquagates_payments_main_acstkn_test" name="aquagates_payments_main_acstkn_test" value="' . esc_attr( $val ) . '" >';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'value' => [] ] ] );
	}

	/**
	 * Input box for setting items
	 */
	public function aquagates_payments_main_user_id_test(){
		$val  = get_option( 'aquagates_payments_main_user_id_test' );
		$html = '';
		$html .= '<label>';
		$html .= '  <input type="text" id="input_aquagates_payments_main_user_id_test" name="aquagates_payments_main_user_id_test" value="' . esc_attr( $val ) . '" >';
		$html .= '</label>';
		echo wp_kses( trim( wp_unslash( $html ) ), [ 'label' => [], 'input' => [ 'type' => [], 'id' => [], 'name' => [], 'value' => [] ] ] );
	}

	/**
	 * Save settings
	 */
	public function update_settings_field( $id ){
		if ( isset( $_POST[ '_wpnonce' ] ) && isset( $_GET[ 'page' ] ) && sanitize_text_field( $_GET[ 'page' ] ) == 'aquagates-payments-setting' ){
			$val = sanitize_text_field( $_POST[ $id ] );
			$val = ( isset( $val ) ) ? $val : '';
			update_option( $id, $val );
		}
	}

	/**
	 * Load each payment
	 */
	public function plugins_loaded_aquagates_payments(){
		include( plugin_dir_path( __FILE__ ) . '../includes/class-aquagates-payments-wcpaygateways-creditcard.php' );
		include( plugin_dir_path( __FILE__ ) . '../includes/class-aquagates-payments-wcpaygateways-link.php' );

	}


}
