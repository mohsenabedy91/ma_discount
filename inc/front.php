<?php
/**
 * set festival price on shop
 *
 * @param $sale_price
 * @param $product
 *
 * @return mixed
 */
function ma_discount_custom_wc_get_sale_price_cbf( $sale_price, $product ): mixed
{
	global $wpdb;
	$querystring = "SELECT $wpdb->postmeta.post_id as post_id FROM $wpdb->postmeta
       WHERE $wpdb->postmeta.meta_key = '_discount_product_id' AND $wpdb->postmeta.meta_value = $product->id";
	$festivals   = $wpdb->get_results( $querystring );

	if ( count( $festivals ) === 0 ) {
		return $sale_price;
	}

	$discount_to_date_time = get_post_meta( $festivals[0]->post_id, '_discount_to_date_time', true );

	if ( (int) $discount_to_date_time < time() ) {
		return $sale_price;
	}

	return get_post_meta( $festivals[0]->post_id, '_discount_price', true );
}

add_filter( 'woocommerce_product_get_sale_price', 'ma_discount_custom_wc_get_sale_price_cbf', 50, 2 );
add_filter( 'woocommerce_product_get_price', 'ma_discount_custom_wc_get_sale_price_cbf', 50, 2 );

/**
 * if product price change this action to was update discount price
 *
 * @param $product_id
 * @param $product
 * @throws JsonException
 */
function ma_discount_my_product_update_cbf( $product_id, $product ) {

	global $wpdb;
	$querystring = "SELECT $wpdb->postmeta.post_id as post_id FROM $wpdb->postmeta
       WHERE $wpdb->postmeta.meta_key = '_discount_product_id' AND $wpdb->postmeta.meta_value = $product_id";
	$festivals   = $wpdb->get_results( $querystring);

	$price                   = json_decode($product, true, 512, JSON_THROW_ON_ERROR)->regular_price;
	$discountPercent         = (int) get_post_meta( $festivals[0]->post_id, '_discount_percent', true );
	$discount_from_date_time = (double) get_post_meta( $festivals[0]->post_id, '_discount_from_date_time', true );
	$discount_to_date_time   = (double) get_post_meta( $festivals[0]->post_id, '_discount_to_date_time', true );

	if ( $discount_to_date_time > (double) time() ) {
		$discount_to_date_time = (double) time();
	}
	$days    = floor( ( ( ( $discount_to_date_time - $discount_from_date_time ) / 60 ) / 60 ) / 24 );
	$Percent = 0;

	if ( $days < 0 ) {
		$days = 0;
	}

	for ( $i = 0; $i <= $days; $i ++ ) {
		$Percent += $discountPercent;
	}
	$ma_discount_product_price = floor( $price - ( ( $price * ( $Percent ) ) / 100 ) );
	update_post_meta( $festivals[0]->post_id, '_discount_price', $ma_discount_product_price );
	update_post_meta( $festivals[0]->post_id, '_discount_sale_price', $ma_discount_product_price );
}

add_action( 'woocommerce_update_product', 'ma_discount_my_product_update_cbf', 10, 2 );