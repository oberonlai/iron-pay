<?php

namespace Irp\Gateways;

use Irp\Utility;

defined( 'ABSPATH' ) || exit;

class Request {

	/**
	 * The gateway instance
	 *
	 * @var WC_Payment_Gateway
	 */
	protected $gateway;

	/**
	 * Constructor
	 *
	 * @param  WC_Payment_Gateway $gateway The payment gateway instance.
	 */
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}

	/**
	 * Build transaction args.
	 *
	 * @param WC_Order $order The order object.
	 * @return array
	 */
	public function get_transaction_args( $order ) {
		$args = apply_filters(
			$this->gateway->id . '_transaction_args',
			array(
				'nonce_str'       => 'nonce',
				'orgno'           => '商店代號',
				'out_trade_no'    => $order->get_order_number(),
				'secondtimestamp' => time(),
				'total_fee'       => $order->get_total() * 100,
			),
			$order
		);

		//$args['sign'] = Utility::generate_sign( $args, $this->gateway->get_secret() );
		return $args;
	}

	/**
	 * Request api and get redirect url
	 *
	 * @param WC_Order $order The order object.
	 * @return void
	 */
	public function build_request( $order ) {
		$order   = new \WC_Order( $order );
		$options = array(
			'method'  => 'POST',
			'timeout' => 60,
			'body'    => $this->get_transaction_args( $order ),
		);

		$response = wp_remote_request( '金流商 API 請求網址' . $this->gateway->get_endpoint(), $options );

		if ( ! is_wp_error( $response ) ) {

			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( '900' === $body['status'] ) {
				return $body['data']['html'];
			} else {
				Utility::log( $body['info'] );
				wc_add_notice( '發生錯誤，請聯繫客服', 'error' );
				return;
			}
		} else {
			Utility::log( $response );
			wc_add_notice( '連線發生錯誤，請聯繫客服', 'error' );
			return;
		}
	}

	/**
	 * Generate the form and redirect to IronPay
	 *
	 * @param WC_Order $order The order object.
	 * @return void
	 */
	public function build_request_form( $order ) {

		$order = wc_get_order( $order );

		try {
			?>
			<div>請稍候重新導向中...</div>
			<form method="post" id="IrpForm" action="<?php echo esc_url( 'API 請求網址' ); ?>" accept="UTF-8" accept-charset="UTF-8">
				<?php
				$fields = $this->get_transaction_args( $order );
				foreach ( $fields as $key => $value ) {
					echo '<input type="hidden" name="' . esc_html( $key ) . '" value="' . esc_html( $value ) . '">';
				}
				?>
			</form>
			<script type="text/javascript">
				document.getElementById('IrpForm').submit();
			</script>
			<?php

		} catch ( Exception $e ) {
			Utility::log( $e->getMessage() . ' ' . $e->getTraceAsString() );
		}
	}
}

