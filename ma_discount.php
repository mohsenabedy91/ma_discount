<?php
/*
Plugin Name: تخفیف دهی
Plugin URI: http://mohsenabedy.ir
Description: پلاگین تخفیف دهی برای محصولات تعریف شده در وردپرس تخفیف گذاری میکنه به اینصورت عمل میکنه که یک محصولی را بهش تخفیف تعریف میکنیم روز اول به همان اندازه که درصد تخفیف گذاشته شده تخفیف داده میشود و روزهای بعد نیز به تخفیف داده شده نسبت به روز اول اضافه میشود که در نهایت تاریخ تخفیف تمام شده و این تخفیف را به سبد خرید خریدار پیشنهادی اضافه میکنم و درنهایت تخفیف برای کالای مورد نظر تموم می شود
Author: محسن عابدی
Version: 1.0.0
Author URI: http://mohsenabedy.ir
Text Domain: ma_discount
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MA_DISCOUNT {

	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var
	 */
	private $ma_discount_includes;

	/**
	 * Start up
	 *
	 * MA_DISCOUNT constructor
	 */
	public function __construct() {

		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {
			register_activation_hook( __FILE__, array( $this, 'ma_activation_cbf' ) );
			register_deactivation_hook( __FILE__, array( $this, 'ma_deactivation_cbf' ) );
			add_action( 'plugin_loaded', array( $this, 'ma_discount_defining_constants_cbf' ), 1 );
			add_action( 'activated_plugin', array( $this, 'ma_discount_activation_redirect_cbf' ), 1 );
			add_action( 'plugin_loaded', array( $this, 'ma_discount_load_textdomain_cbf' ), 1 );
			add_action( 'plugin_loaded', array( $this, 'ma_discount_set_includes_cbf' ), 1 );
			add_action( 'plugin_loaded', array( $this, 'ma_discount_load_includes_cbf' ), 1 );
		} else {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( plugin_basename( __FILE__ ), true );
			wp_die( __( 'You have to Woocommerce plugin install and used discount plugin!', 'ma_discount' ) );
		}
	}

	/**
	 * MA_DISCOUNT ma_activation_cbf
	 *
	 * Create options ma_discount_opts and ma_registration_redirect on active plugin
	 */
	public function ma_activation_cbf(): void
	{

		global $wpdb;
		flush_rewrite_rules();

		if ( ! get_option( 'ma_discount_opts' ) ) {
			add_option( 'ma_discount_opts' );
			add_option( 'ma_discount_carousel_image' );
		}

		if ( ! get_option( 'ma_registration_redirect' ) ) {
			add_option( 'ma_registration_redirect', '1' );
		}
		if ( ! wp_next_scheduled( 'ma_discount_event' ) ) {
			wp_schedule_event( get_gmt_from_date( date( 'Y-m-d H:i:s', strtotime( current_time( '1:00:00' ) ) ), 'U' ), 'daily', 'ma_discount_event' );
		}

		$table_name      = $wpdb->prefix . 'discount_logs';
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name(
		id int(9) NOT NULL AUTO_INCREMENT,
		order_id varchar(50) NOT NULL,
		discount_id varchar(50) NOT NULL,
		item_id varchar(50) NOT NULL,
		product_id varchar(50) NOT NULL,
		product_price varchar(50) NOT NULL,
		customer_user varchar(50) NOT NULL,
		sell_price varchar(50) NOT NULL,
		created_date varchar(50) NOT NULL,
		order_time varchar(50) NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * MA_DISCOUNT ma_deactivation_cbf
	 */
	public function ma_deactivation_cbf(): void
	{
		wp_clear_scheduled_hook( 'ma_discount_event' );
		flush_rewrite_rules();
	}

	/**
	 * @param $plugin
	 *
	 * MA_DISCOUNT ma_discount_activation_redirect_cbf
	 */
	public function ma_discount_activation_redirect_cbf( $plugin ): void
	{
		if ( $plugin === plugin_basename( __FILE__ ) ) {
			exit( wp_redirect( admin_url( 'edit.php?post_type=ma_discount' ) ) );
		}
	}

	/**
	 * MA_DISCOUNT ma_discount_constants
	 *
	 * Create a lot of const and use another file
	 */
	public function ma_discount_defining_constants_cbf(): void
	{
		! defined( 'MA_DISCOUNT_VERSION' ) && define( 'MA_DISCOUNT_VERSION', '1.0.0' );
		! defined( 'MA_DISCOUNT_PLUGIN_NAME' ) && define( 'MA_DISCOUNT_PLUGIN_NAME', 'ma_discount' );
		! defined( 'MA_DISCOUNT_BASE_PLUGIN_DIR' ) && define( 'MA_DISCOUNT_BASE_PLUGIN_DIR', MA_DISCOUNT_PLUGIN_NAME . '/' . MA_DISCOUNT_PLUGIN_NAME . '.php' );
		! defined( 'MA_DISCOUNT_PLUGIN_PATH' ) && define( 'MA_DISCOUNT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		! defined( 'MA_DISCOUNT_PLUGIN_URL' ) && define( 'MA_DISCOUNT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		! defined( 'MA_DISCOUNT_LANGUAGES_DIR' ) && define( 'MA_DISCOUNT_LANGUAGES_DIR', MA_DISCOUNT_PLUGIN_NAME . '/languages' );
		! defined( 'MA_DISCOUNT_INC_DIR' ) && define( 'MA_DISCOUNT_INC_DIR', 'inc' );
		! defined( 'MA_DISCOUNT_CLASS_DIR' ) && define( 'MA_DISCOUNT_CLASS_DIR', 'class' );
		! defined( 'MA_DISCOUNT_IMG_DIR' ) && define( 'MA_DISCOUNT_IMG_DIR', 'assets/images' );
		! defined( 'MA_DISCOUNT_CSS_DIR' ) && define( 'MA_DISCOUNT_CSS_DIR', 'assets/css' );
		! defined( 'MA_DISCOUNT_JS_DIR' ) && define( 'MA_DISCOUNT_JS_DIR', 'assets/js' );
		! defined( 'WP_POST_REVISIONS' ) && define( 'WP_POST_REVISIONS', 'false' );
	}

	/**
	 * MA_DISCOUNT ma_discount_load_textdomain
	 *
	 * Create multilingual textdomain and defined addresses file
	 */
	public function ma_discount_load_textdomain_cbf(): void
	{
		load_plugin_textdomain( MA_DISCOUNT_PLUGIN_NAME, false, MA_DISCOUNT_LANGUAGES_DIR );
	}

	/**
	 * MA_DISCOUNT ma_discount_set_includes
	 *
	 * defined all file include this plugin
	 */
	public function ma_discount_set_includes_cbf(): void
	{
		$this->ma_discount_includes = array(
			'admin' => array(
				MA_DISCOUNT_INC_DIR . '/jdf.php',
				MA_DISCOUNT_INC_DIR . '/page.php',
				MA_DISCOUNT_INC_DIR . '/page_settings.php',
				MA_DISCOUNT_INC_DIR . '/page_logs.php',
				MA_DISCOUNT_INC_DIR . '/admin.php',
				MA_DISCOUNT_INC_DIR . '/functions.php',
				MA_DISCOUNT_INC_DIR . '/cron.php',
				MA_DISCOUNT_INC_DIR . '/ajax.php',
				MA_DISCOUNT_INC_DIR . '/widgets.php',
				MA_DISCOUNT_INC_DIR . '/post_type.php',
				MA_DISCOUNT_CLASS_DIR . '/ma_discount_post_type.php',
			),
			'front' => array(
				MA_DISCOUNT_INC_DIR . '/cron.php',
				MA_DISCOUNT_INC_DIR . '/jdf.php',
				MA_DISCOUNT_INC_DIR . '/functions.php',
				MA_DISCOUNT_INC_DIR . '/front.php',
				MA_DISCOUNT_INC_DIR . '/ajax.php',
				MA_DISCOUNT_INC_DIR . '/widgets.php',
			)
		);
	}

	/**
	 * MA_DISCOUNT ma_discount_load_includes
	 *
	 * load all file include this plugin
	 */
	public function ma_discount_load_includes_cbf(): void
	{
		$ma_discount_includes = $this->ma_discount_includes;
		if ( $ma_discount_includes ) {
			foreach ( $ma_discount_includes as $key => $files ) {
				switch ( $key ) {
					case 'admin':
						if ( is_admin() ) {
							foreach ( $files as $file ) {
								/**
								 * @noinspection PhpIncludeInspection
								 */
								require_once $file;
							}
						}
						break;
					case 'front':
						foreach ( $files as $file ) {
							/**
							 * @noinspection PhpIncludeInspection
							 */
							require_once $file;
						}
						break;
					default:
						foreach ( $files as $file ) /**
						 * @noinspection PhpIncludeInspection
						 */ {
							require_once $file;
						}
						break;
				}
			}
		}
	}
}

new MA_DISCOUNT();