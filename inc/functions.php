<?php

function ma_discount_table_logs() {
	$i = 0;
	foreach ( ma_discount_get_logs() as $log ) {
		$i ++;
		$percent       = ( $log->sell_price * 100 ) / $log->product_price;
		$customer_name = get_user_meta( $log->customer_user, 'first_name', true ) . ' ' . get_user_meta( $log->customer_user, 'last_name', true );

		if ( $log->customer_user === 0 ) {
			$customer_name = __( 'Guest', 'ma_discount' );
		}

		echo '<tr>';
		echo '<td>' . $i . '</td>';
		echo '<td>' . get_the_title( $log->product_id ) . '</td>';
		echo '<td>' . jdate( 'Y/m/d', $log->created_date, '', 'Asia/Tehran', 'en' ) . '</td>';
		echo '<td>' . jdate( 'Y/m/d', $log->order_time, '', 'Asia/Tehran', 'en' ) . '</td>';
		echo '<td>' . $customer_name . '</td>';
		echo '<td>' . ( 100 - $percent ) . ' %' . '</td>';
		echo '<td>' . wc_price( $log->sell_price ) . '</td>';
		echo '</tr>';
	}
}

function ma_discount_get_logs(): object|array|null
{
	global $wpdb;
	$table_name  = $wpdb->prefix . 'discount_logs';
	$querystring = "SELECT * FROM $table_name";

	return $wpdb->get_results( $querystring );
}

/**
 * @param $order_id
 * @param $items
 */
function action_function_name_8036( $order_id, $items ) {

	// ایجا وضعیت سفارش تغییر پیدا میکنه
	//TODO
}

add_action( 'woocommerce_saved_order_items', 'action_function_name_8036', 10, 2 );

/**
 * save meta data after add new order for discount logs
 *
 * @throws Exception
 */
function ma_discount_save_Meta_Data_cbf( $item_id, $item, $order_id ) {

	global $wpdb;
	$time       = time();
	$sell_price = wc_get_order_item_meta( $item_id, '_line_subtotal' ) / wc_get_order_item_meta( $item_id, '_qty' );
	$table_name = $wpdb->prefix . 'discount_logs';
	$product_id = wc_get_order_item_meta( $item_id, '_product_id' );

	$querystring = "SELECT $wpdb->postmeta.post_id as post_id FROM $wpdb->postmeta
       WHERE $wpdb->postmeta.meta_key = '_discount_product_id' AND $wpdb->postmeta.meta_value = $product_id";
	$festivals   = $wpdb->get_results( $querystring, ARRAY_A );

	foreach ( $festivals as $festival ) {
		if ( $time >= get_post_meta( $festival["post_id"], '_discount_from_date_time', true ) &&
		     $time <= get_post_meta( $festival["post_id"], '_discount_to_date_time', true ) ) {

			$wpdb->insert( $table_name, array(
				'order_id'      => $order_id,
				'discount_id'   => $festival['post_id'],
				'item_id'       => $item_id,
				'product_id'    => $product_id,
				'product_price' => get_post_meta( $product_id, '_regular_price', true ),
				'customer_user' => get_post_meta( $order_id, '_customer_user', true ),
				'sell_price'    => $sell_price,
				'created_date'  => get_post_meta( $festival["post_id"], '_discount_from_date_time', true ),
				'order_time'    => $time,
			) );
			break;
		}
	}
}

add_action( 'woocommerce_new_order_item', 'ma_discount_save_Meta_Data_cbf', 10, 3 );
