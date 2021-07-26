<?php
add_action( 'wp_ajax_ma_discount_get_data', 'ma_discount_get_data' );
add_action( 'wp_ajax_nopriv_ma_discount_get_data', 'ma_discount_get_data' );

function ma_discount_get_data() {
	$attached_file     = get_post_meta( $_POST['discount_product'], '_thumbnail_id', true );
	$_wp_attached_file = wp_get_upload_dir()["baseurl"] . '/' . get_post_meta( $attached_file, '_wp_attached_file', true );

	echo wp_json_encode( [
		'ma_discount_product_price' => wc_price( get_post_meta( $_POST['discount_product'], '_price', true ) ),
		'ma_discount_product_image' => $_wp_attached_file
	] );
	die;
}