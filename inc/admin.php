<?php

if ( get_option( 'ma_registration_redirect' ) === '1' ) {

	function ma_discount_admin_notice_welcome_plugin() {
		?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'WPlugin activated.', 'ma_discount' ); ?></p>
        </div>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Welcome to The Discount Plugin. Please Add Discount For Product.', 'ma_discount' ); ?></p>
        </div>
		<?php
	}

	delete_option( 'ma_registration_redirect' );
	add_action( 'admin_notices', 'ma_discount_admin_notice_welcome_plugin' );
}
