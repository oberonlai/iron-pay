<?php

/**
 * @link              https://oberonlai.blog
 * @since             1.0.0
 * @package           irp
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce 鐵人付外掛
 * Plugin URI:        https://oberonlai.blog
 * Description:       新增 WooCommerce 鐵人支付金流
 * Version:           1.0.0
 * Author:            Oberon Lai
 * Author URI:        https://oberonlai.blog
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       irp
 * Domain Path:       /languages
 * WC tested up to: 5.6.0
 * WC requires at least: 5
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check WooCommerce Activied
 */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		/**
		 * Error admin notice
		 */
		function require_woocommerce_notice() {
			echo '<div class="error"><p>外掛啟用失敗，需要安裝並啟用 WooCommerce 5.3 以上版本。</p></div>';
		}
		add_action( 'admin_notices', 'require_woocommerce_notice' );
		return;
	}
}

define( 'IRP_PLUGIN_VERSION', '1.0.0' );
define( 'IRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'IRP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IRP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoload
 */
require_once IRP_PLUGIN_DIR . 'vendor/autoload.php';
\A7\autoload( IRP_PLUGIN_DIR . 'src' );
