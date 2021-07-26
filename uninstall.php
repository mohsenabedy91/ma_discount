<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'rg-ps-opts' );
delete_option( 'rg-ps-logs-opts' );

global $wpdb;
$table_name = $wpdb->prefix . 'discount_logs';
$sql        = "DROP TABLE IF EXISTS $table_name";
$wpdb->query( $sql );
