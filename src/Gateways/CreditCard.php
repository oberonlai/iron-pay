<?php

namespace Irp\Gateways;

defined( 'ABSPATH' ) || exit;

/**
 * Set payment class
 */
function add_irp_credit_card() {
	class CreditCard extends \WC_Payment_Gateway {

		public function __construct() {
			$this->id                 = 'irp_credit_card';
			$this->icon               = '';
			$this->has_fields         = false;
			$this->method_title       = '鐵人付信用卡';
			$this->method_description = '使用鐵人付信用卡付款';

			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		public static function register() {
			add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'add_gateway_class' ) );
		}

		public function add_gateway_class( $methods ) {
			$methods[] = __CLASS__;
			return $methods;
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => '啟用/停用',
					'label'   => '啟用付款',
					'type'    => 'checkbox',
					'default' => 'no',
				),
				'title'       => array(
					'title' => '付款方式名稱',
					'type'  => 'text',
				),
				'description' => array(
					'title' => '付款方式描述',
					'type'  => 'textarea',
					'css'   => 'max-width: 400px;',
				),
			);
		}

		/**
		 * Process payment
		 *
		 * @param string $order_id The order id.
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order   = new \WC_Order( $order_id );
			$request = new Request( $this );
			$return_url = $request->build_request( $order );
			return array(
				'result'   => 'success',
				'redirect' => $return_url,
			);
		}
	}

	CreditCard::register();

}
add_action( 'plugins_loaded', 'Irp\Gateways\add_irp_credit_card' );

