<?php
namespace Irp\Gateways;

use Irp\Utility;

defined( 'ABSPATH' ) || exit;

/**
 * Receive response from ironpay.
 */
class Response {

	/**
	 * Initialize and add hooks
	 *
	 * @return void
	 */
	public static function init() {

		// Online Payment listener.
		add_action( 'woocommerce_api_iron-pay', array( __CLASS__, 'receive_response' ) );
		add_action( 'ironpay_online_response', array( __CLASS__, 'valid_response' ) );

		// Offline Payment listener.
		add_action( 'woocommerce_api_iron-pay-offline', array( __CLASS__, 'receive_response' ) );
		add_action( 'ironpay_offline_response', array( __CLASS__, 'valid_response_offline' ) );

	}

	/**
	 * Receive response from ironpay
	 *
	 * @return void
	 */
	public static function receive_response() {

		if ( ! empty( $_REQUEST ) ) {
			$params = wc_clean( wp_unslash( $_REQUEST ) );
			$args   = array(
				'authcode'        => $params['authcode'],
				'nonce_str'       => $params['nonce_str'],
				'orderdate'       => $params['orderdate'],
				'orgno'           => $params['orgno'],
				'out_trade_no'    => $params['out_trade_no'],
				'periods'         => $params['periods'],
				'result'          => $params['result'],
				'secondtimestamp' => $params['secondtimestamp'],
				'status'          => $params['status'],
				'total_fee'       => $params['total_fee'],
			);

			$sign = Utility::generate_sign( $args, Utility::get_secret() );

			if ( $sign === $params['sign'] ) {
				if ( current_action() === 'woocommerce_api_iron-pay' ) {
					do_action( 'ironpay_online_response', $params );
				} elseif ( current_action() === 'woocommerce_api_iron-pay-offline' ) {
					do_action( 'ironpay_offline_response', $params );
				}
			}
		}
	}

	/**
	 * Receive online payment
	 *
	 * @param array $params The post data received from ironpay.
	 * @return void
	 */
	public static function valid_response( $params ) {
		global $woocommerce;
		$order = new \WC_Order( $params['out_trade_no'] );
		if ( $order ) {
			if ( '0000' === $status ) {
				$order->payment_complete();
				$order->reduce_order_stock();
			} else {
				$order->update_status( 'on-hold' );
			}
			$woocommerce->cart->empty_cart();
			update_post_meta( $order->get_id(), '_irp_resp_code', $params['status'] );
			update_post_meta( $order->get_id(), '_irp_resp_result', $params['result'] );
			$order->add_order_note( '鐵人付交易結果：' . $params['result'], true );
			wp_safe_redirect( $order->get_checkout_order_received_url() );
			exit;
		}
	}

	/**
	 * Receive offline payment
	 *
	 * @param array $params The post data received from ironpay.
	 * @return void
	 */
	public static function valid_response_offline( $params ) {
		global $woocommerce;
		$order = new \WC_Order( $params['out_trade_no'] );
		if ( $order ) {
			if ( '0000' === $status ) {
				$order->payment_complete( $buysafe_no );
				$order->reduce_order_stock();	
			}
			$woocommerce->cart->empty_cart();
			update_post_meta( $order->get_id(), '_irp_resp_code', $params['status'] );
			update_post_meta( $order->get_id(), '_irp_resp_result', $params['result'] );
			$order->add_order_note( '鐵人付交易結果：' . $params['result'], true );
			wp_send_json( 'success' );
			exit;
		}
	}

	/**
	 * Get order object from order id.
	 *
	 * @param array $posted The posted data.
	 * @return WC_Order
	 */
	private static function get_order( $params ) {
		return wc_get_order( $params['out_trade_no'] );
	}

	/**
	 * Update order metabox
	 *
	 * @param object $order WC_Order Object.
	 * @param string $status Receive stauts.
	 * @param string $result Receive result.
	 */
	private static function update_order_meta( $order, $status, $result ) {
		update_post_meta( $order->get_id(), '_irp_resp_code', $status );
		update_post_meta( $order->get_id(), '_irp_resp_result', $result );
		$order->add_order_note( '鐵人付交易結果：' . $result, true );
	}
}

Response::init();
