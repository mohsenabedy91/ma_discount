<?php

/**
 * add sub menu on plugin
 */
function ma_discount_admin_menu_cbf()
{
	add_submenu_page(
		'edit.php?post_type=ma_discount',
		__('Logs', 'ma_discount'),
		__('Logs', 'ma_discount'),
		'manage_options',
		'ma_discount_logs',
		'ma_discount_logs_cbf'
	);
	add_submenu_page(
		'edit.php?post_type=ma_discount',
		__('Settings', 'ma_discount'),
		__('Settings', 'ma_discount'),
		'manage_options',
		'ma_discount_settings',
		'ma_discount_settings_cbf'
	);
}

add_action('admin_menu', 'ma_discount_admin_menu_cbf');

function ma_discount_add_action_links_cbf($links)
{
//	$links[] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=ma_discount' ) ) . '">' . esc_html__( 'Discounts', 'ma_discount' ) . '</a>';
//	$links[] = '<a href="' . esc_url( admin_url( 'edit.php?post_type=ma_discount&page=ma_discount_logs' ) ) . '">' . esc_html__( 'Logs', 'ma_discount' ) . '</a>';
	$links[] = '<a href="' . esc_url(admin_url('edit.php?post_type=ma_discount&page=ma_discount_settings')) . '">' . esc_html__('Settings', 'ma_discount') . '</a>';

	return $links;
}

add_filter('plugin_action_links_' . MA_DISCOUNT_BASE_PLUGIN_DIR, 'ma_discount_add_action_links_cbf');
