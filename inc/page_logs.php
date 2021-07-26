<?php
//نام محصول، تاریخ ایجاد، تاریخ فروش، نام
//خریدار، درصد تخفیف فروخته شده و مبلغ فروخته شده
function ma_discount_logs_cbf() {
	?>
    <div class="wrap ma_discount_logs">

        <h2><?php esc_html_e( 'All Logs', 'ma_discount' ); ?></h2>
        <table class="widefat fixed">
            <thead>
            <tr>
                <th width="5%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Row', 'ma_discount' ); ?></th>
                <th width="30%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Product', 'ma_discount' ); ?></th>
                <th width="11%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Created date', 'ma_discount' ); ?></th>
                <th width="11%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Sell date', 'ma_discount' ); ?></th>
                <th width="21%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Purchaser name', 'ma_discount' ); ?></th>
                <th width="11%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Discount percent', 'ma_discount' ); ?></th>
                <th width="11%" class="manage-column"
                    scope="col"><?php esc_html_e( 'Sell price', 'ma_discount' ); ?></th>
            </tr>
            </thead>
            <tbody>

	            <?php ma_discount_table_logs(); ?>
            </tbody>
        </table>
    </div>
	<?php
}

/* Admin Enqueue */
function wl_ps_logs_admin_enqueue( $hook_suffix ) {
	if ( 'post-submitter_page_post-submitter-logs' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style( 'ma_discount_style', MA_DISCOUNT_PLUGIN_URL . '/' . MA_DISCOUNT_CSS_DIR . '/ma_discount.css', array(), MA_DISCOUNT_VERSION );
}

add_action( 'admin_enqueue_scripts', 'wl_ps_logs_admin_enqueue' );
