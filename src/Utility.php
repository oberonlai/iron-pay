<?php

namespace Irp;

class Utility {
	/**
	 * Whether or not logging is enabled.
	 *
	 * @var boolean
	 */
	public static $log_enabled = true;

	/**
	 * WC_Logger instance.
	 *
	 * @var WC_Logger Logger instance
	 * */
	public static $log = false;

	/**
	 * Log method.
	 *
	 * @param string $message The message to be logged.
	 * @param string $level The log level. Optional. Default 'info'. Possible values: emergency|alert|critical|error|warning|notice|info|debug.
	 * @return void
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = new \WC_Logger();
			}
			self::$log->log( $level, $message, array( 'source' => 'iron-pay' ) );
		}
	}

	/**
	 * Build pass code for api usage.
	 *
	 * @param string $args args.
	 * @param string $secret API secret.
	 * @return string
	 */
	public static function generate_sign( $args, $secret ) {
		$post_datastr = '';

		foreach ( $args as $name => $value ) {
			if ( ! is_array( $value ) ) {
				if ( $value !== null && $value !== '' ) {
					$post_datastr = $post_datastr . $name . '=' . $value . '&';
				}
			}
		}
		$tmp_array = explode( '&', trim( $post_datastr, '&' ) );
		sort( $tmp_array, SORT_STRING );
		$source     = implode( '&', $tmp_array );
		$source_key = $source . '&key=' . $secret;
		$sign       = strtoupper( md5( $source_key ) );
		return $sign;
	}

	/**
	 * Get stord id
	 *
	 * @return string
	 */
	public static function get_store_id() {
		$store_id = get_option( 'irp_payment_orgno' );
		if ( empty( $store_id ) ) {
			return '未輸入商店代號';
		}

		if ( ! is_numeric( $store_id ) ) {
			return '商店代號限定數字';
		}

		if ( strlen( $store_id ) !== 8 ) {
			return '商店代號須為 8 碼';
		}
		return $store_id;
	}

	/**
	 * Generate random string for sign
	 *
	 * @param string $length nonce length.
	 * @return string
	 */
	public static function generate_nonce( $length = 8 ) {
		$characters        = '0123456789';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
		}
		return $random_string;
	}

	/**
	 * Get secret
	 *
	 * @return string
	 */
	public static function get_secret() {
		$secret = ( 'yes' === get_option( 'irp_payment_testmode_enabled' ) ) ? get_option( 'irp_payment_testmode_secret' ) : get_option( 'irp_payment_secret' );
		return $secret;
	}

}

