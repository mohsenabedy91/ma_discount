<?php
add_action( 'ma_discount_event', 'ma_discount_cron_event' );

/**
 * It updates the price of the festival product every night
 */
function ma_discount_cron_event() {

	$defaults = array(
		'numberposts' => null,
		'post_type'   => 'ma_discount',
	);
	$posts    = get_posts( $defaults );
	foreach ( $posts as $post ) {
		$ma_discount_product_id  = (int) @get_post_meta( $post->ID, '_discount_product_id', true );
		$price                   = get_post_meta( $ma_discount_product_id, '_regular_price', true );
		$discountPercent         = (int) get_post_meta( $post->ID, '_discount_percent', true );
		$discount_from_date_time = (double) get_post_meta( $post->ID, '_discount_from_date_time', true );
		$discount_to_date_time   = (double) get_post_meta( $post->ID, '_discount_to_date_time', true );

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
		update_post_meta( $post->ID, '_discount_price', $ma_discount_product_price );
		update_post_meta( $post->ID, '_discount_sale_price', $ma_discount_product_price );
	}
}
