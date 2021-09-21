<?php

namespace Irp;

defined( 'ABSPATH' ) || exit;

function set_options() {
	class Options extends \WC_Settings_Page {

		public function __construct() {
			$this->id = 'irp_setting_gateway';
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 51 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		}

		/**
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @param array $settings_tabs setting tab.
		 * @return array $settings_tabs setting tab.
		 */
		public function add_settings_tab( $settings_tabs ) {
			$settings_tabs['irp_setting_gateway'] = '鐵人付設定';
			return $settings_tabs;
		}

		/**
		 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
		 *
		 * @return array Array of settings for @see woocommerce_admin_fields() function.
		 */
		public function set_fields( $section = null ) {
			$settings = array(
				array(
					'title' => '一般設定',
					'type'  => 'title',
					'id'    => 'irp_general_setting',
				),
				array(
					'title'   => '除錯資訊',
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => sprintf( '紀錄日誌於以下路徑：<code>%s</code>', wc_get_log_file_path( 'iron-pay' ) ),
					'id'      => 'irp_payment_debug_log_enabled',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'irp_general_setting',
				),
				array(
					'title' => '商家資料設定',
					'type'  => 'title',
					'id'    => 'irp_payment_api_settings',
				),
				array(
					'title' => '測試模式',
					'type'  => 'checkbox',
					'desc'  => '如果要使用測試模式請勾選，未勾選則為正式交易環境',
					'id'    => 'irp_payment_testmode_enabled',
				),
				array(
					'title' => '測試商家',
					'type'  => 'text',
					'desc'  => '請輸入測試商家代號',
					'id'    => 'irp_payment_testmode_orgno',
				),
				array(
					'title' => '測試金鑰',
					'type'  => 'text',
					'desc'  => '請輸入測試金鑰',
					'id'    => 'irp_payment_testmode_secret',
				),
				array(
					'title' => '正式商家',
					'type'  => 'text',
					'desc'  => '請輸入正式商家代號',
					'id'    => 'irp_payment_orgno',
				),
				array(
					'title' => '正式金鑰',
					'type'  => 'text',
					'desc'  => '請輸入正式金鑰',
					'id'    => 'irp_payment_secret',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'irp_payment_api_settings',
				),
			);
			return $settings;
		}

		/**
		 * Render fields html.
		 */
		public function output() {
			global $current_section, $hide_save_button;
			$settings = $this->set_fields( $current_section );
			\WC_Admin_Settings::output_fields( $settings );
		}

		/**
		 * Save fields value.
		 */
		public function save() {
			global $current_section;
			$settings = $this->set_fields( $current_section );
			\WC_Admin_Settings::save_fields( $settings );
		}

	}

	return new Options();
}

add_filter( 'woocommerce_get_settings_pages', 'irp\set_options' );
