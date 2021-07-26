<?php
function ma_discount_admin_menu_post_type_cbf() {
	global $ma_discount;
	$labels = array(
		'name'               => __( 'Discounts', 'ma_discount' ),
		'singular_name'      => __( 'Discount', 'ma_discount' ),
		'menu_name'          => __( 'Discounts', 'ma_discount' ),
		'name_admin_bar'     => __( 'Discount', 'ma_discount' ),
		'add_new'            => __( 'Add New', 'ma_discount' ),
		'add_new_item'       => __( 'Add New Discount', 'ma_discount' ),
		'new_item'           => __( 'New Discount', 'ma_discount' ),
		'edit_item'          => __( 'Edit Discount', 'ma_discount' ),
		'view_item'          => __( 'View Discount', 'ma_discount' ),
		'all_items'          => __( 'All Discounts', 'ma_discount' ),
		'search_items'       => __( 'Search Discounts', 'ma_discount' ),
		'parent_item_colon'  => __( 'Parent Discount', 'ma_discount' ),
		'not_found'          => __( 'Not Discounts Found', 'ma_discount' ),
		'not_found_in_trash' => __( 'Not Discounts Found In Trash', 'ma_discount' ),
	);
	$args   = array(
		'labels'            => $labels,
		'description'       => __( "The title have to at least 5 character and the start and end dates of the discount should not be overlap for a product", "ma_discount" ),
		'public'            => false,
		'public_queryable'  => false,
		'show_ui'           => true,
		'show_in_menu'      => true,
		'show_in_nav_menus' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'ma_discount' ),
		'capability_type'   => 'post',
		'has_archive'       => false,
		'hierarchical'      => true,
		'menu_position'     => 85,
		'menu_icon'         => 'dashicons-superhero-alt',
		'supports'          => array( 'title' ),
	);
	register_post_type( 'ma_discount', $args );
}

add_action( 'init', 'ma_discount_admin_menu_post_type_cbf' );

/**
 * remove Quick Edit AND View actions from post type table
 *
 * @param array $actions
 * @param null $post
 *
 * @return array
 */
function ma_discount_disable_quick_edit_cbf(array $actions = array(), $post = null ): array
{

	if ( get_post_type() !== 'ma_discount' ) {
		return $actions;
	}

	if ( isset( $actions['inline hide-if-no-js'] ) ) {
		unset($actions['inline hide-if-no-js'], $actions['view']);
	}

	return $actions;
}

add_filter( 'page_row_actions', 'ma_discount_disable_quick_edit_cbf', 10, 2 );

/**
 * add new columns on post type table
 *
 * @param $columns
 *
 * @return mixed
 */
function ma_discount_columns_filter_cbf( $columns ): mixed
{
	$columns['ProductName']       = __( 'Product', 'ma_discount' );
	$columns['ProductPrice']      = __( 'Price', 'ma_discount' );
	$columns['DiscountPrice']     = __( 'Discount price', 'ma_discount' );
	$columns['DiscountRemaining'] = __( 'Remaining', 'ma_discount' );
	$columns['DiscountStatus']    = __( 'Status', 'ma_discount' );
	$columns['StartDiscount']     = __( 'Start date', 'ma_discount' );

	return $columns;
}

add_filter( 'manage_ma_discount_posts_columns', 'ma_discount_columns_filter_cbf', 10, 1 );

/**
 * add sortable action to post type table columns
 *
 * @param $columns
 *
 * @return mixed
 */
function ma_discount_order_column_register_sortable_cbf( $columns ): mixed
{
	$columns['ProductName']       = __( 'Product', 'ma_discount' );
	$columns['ProductPrice']      = __( 'Price', 'ma_discount' );
	$columns['DiscountPrice']     = __( 'Discount price', 'ma_discount' );
	$columns['DiscountRemaining'] = __( 'Remaining', 'ma_discount' );
	$columns['DiscountStatus']    = __( 'Status', 'ma_discount' );
	$columns['StartDiscount']     = __( 'Start date', 'ma_discount' );

	return $columns;
}

add_filter( 'manage_edit-ma_discount_sortable_columns', 'ma_discount_order_column_register_sortable_cbf' );

/**
 * push the data on post type table
 *
 * @param $name
 * @param $post_id
 */
function ma_discount_columns_filters_cbf( $name, $post_id ) {

	$ma_discount_post_type_opts = unserialize( @get_post_meta( $post_id, 'ma_discount_base_meta', true ) , ['allowed_classes' => false]);
	$price                      = (int) @get_post_meta( $ma_discount_post_type_opts['discount_product'], '_regular_price', true );
	$stock                      = @get_post_meta( $ma_discount_post_type_opts['discount_product'], '_stock_status', true );
	$discountPercent            = (int) @get_post_meta( $post_id, '_discount_percent', true );
	$discount_from_date_time    = (double) @get_post_meta( $post_id, '_discount_from_date_time', true );
	$discount_to_date_time      = (double) @get_post_meta( $post_id, '_discount_to_date_time', true );
	if ( $name === 'StartDiscount' ) {
		echo @$ma_discount_post_type_opts['discount_from_date'];
	}
	if ( $name === 'ProductName' ) {
		echo @get_post( $ma_discount_post_type_opts['discount_product'] )->post_title;
	}
	if ( $name === 'ProductPrice' ) {
		echo wc_price( $price );
	}
	if ( $name === 'DiscountRemaining' ) {
		if ( floor( ( ( ( $discount_to_date_time - (double) time() ) / 60 ) / 60 ) / 24 ) < 0 ) {
			echo __( 'This discount is over!', 'ma_discount' );
		} else {
			echo floor( ( ( ( $discount_to_date_time - (double) time() ) / 60 ) / 60 ) / 24 ) . ' ' . __( 'Days', 'ma_discount' );
		}
	}
	if ( $name === 'DiscountPrice' ) {
		$days    = floor( ( ( ( ( ( $discount_to_date_time < (double) time() ) ? $discount_to_date_time : (double) time() ) - $discount_from_date_time ) / 60 ) / 60 ) / 24 );
		$Percent = 0;
		for ( $i = 0; $i <= $days; $i ++ ) {
			$Percent += $discountPercent;
		}
		$DiscountPrice = wc_price( $price );
		if ( $discount_from_date_time < time() ) {
			$DiscountPrice = wc_price( floor( $price - ( ( $price * ( $Percent ) ) / 100 ) ) );
		}

		echo $DiscountPrice;
	}
	if ( $name === 'DiscountStatus' ) {
		if ( floor( ( ( ( $discount_to_date_time - (double) time() ) / 60 ) / 60 ) / 24 ) < 0 ) {
			echo __( 'Purchased', 'ma_discount' );
		} elseif ( $stock !== "instock" ) {
			echo $stock . ' ' . __( 'Sold', 'ma_discount' );
		} else {
			echo __( 'Waiting sale', 'ma_discount' );
		}
	}
}

add_filter( 'manage_ma_discount_posts_custom_column', 'ma_discount_columns_filters_cbf', 10, 3 );

/**
 * unset columns function
 *
 * @param $columns
 *
 * @return mixed
 */
function ma_discount_unset_column_date_cbf( $columns ): mixed
{
	unset( $columns['date'] );

	return $columns;
}

add_filter( 'manage_ma_discount_posts_columns', 'ma_discount_unset_column_date_cbf' );
