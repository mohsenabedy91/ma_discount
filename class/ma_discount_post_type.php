<?php

class MAPOSTTYPE
{

	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'ma_discount_add_meta_boxes_cbf'));
		add_action('save_post_ma_discount', array($this, 'ma_discount_save_post_meta_cbf'), 10, 3);
		add_action('pre_post_update', array($this, 'ma_discount_pre_save_post_meta_cbf'), 10, 2);
		add_action('admin_notices', array($this, 'ma_discount_my_notification_cbf'));
		add_action('admin_enqueue_scripts', array($this, 'ma_discount_enqueue_scripts_cbf'), 1000);
	}

	public function ma_discount_enqueue_scripts_cbf(): void
	{

		wp_enqueue_style('ma_discount_datepicker-css', MA_DISCOUNT_PLUGIN_URL . MA_DISCOUNT_CSS_DIR . '/persian-datepicker.css');
		wp_enqueue_script('ma_discount_jquery', MA_DISCOUNT_PLUGIN_URL . MA_DISCOUNT_JS_DIR . '/jquery.min.js', array(), '3.4.1', true);
		wp_enqueue_script('ma_discount_datepicker-js', MA_DISCOUNT_PLUGIN_URL . MA_DISCOUNT_JS_DIR . '/persian-datepicker.min.js', array(), MA_DISCOUNT_VERSION, true);
		wp_enqueue_script('ma_discount_admin_js', MA_DISCOUNT_PLUGIN_URL . MA_DISCOUNT_JS_DIR . '/admin.js', array(), MA_DISCOUNT_VERSION, true);
	}

	public function ma_discount_add_meta_boxes_cbf(): void
	{
		add_meta_box('ma_discount_description_fields', __('Description', 'ma_discount'), array(
			$this,
			'ma_discount_post_type_description_cbf'
		), 'ma_discount', 'side');

		add_meta_box('ma_discount_id_fields', __('Discount ID', 'ma_discount'), array(
			$this,
			'ma_discount_generate_id_html_metabox_cbf'
		), 'ma_discount', 'side');

		add_meta_box('ma_discount_show_product_fields_after_sell', __('Show After Finish Discount In The Carousel ', 'ma_discount'), array(
			$this,
			'ma_discount_show_product_after_sell_cbf'
		), 'ma_discount', 'normal');

		add_meta_box('ma_discount_product_fields', __('Product', 'ma_discount'), array(
			$this,
			'ma_discount_generate_product_html_metabox_cbf'
		), 'ma_discount', 'normal');
		add_meta_box('ma_discount_to_date_fields', __('Discount Date', 'ma_discount'), array(
			$this,
			'ma_discount_generate_date_html_metabox_cbf'
		), 'ma_discount', 'normal');
		add_meta_box('ma_discount_percent_fields', __('Discount Percent', 'ma_discount'), array(
			$this,
			'ma_discount_generate_percent_html_metabox_cbf'
		), 'ma_discount', 'normal');
	}

	public function ma_discount_post_type_description_cbf(): void
	{
		global $wp_post_types;

		$obj = $wp_post_types['ma_discount'];
		echo $obj->description;
	}

	public function ma_discount_generate_id_html_metabox_cbf(): void
	{
		global $post;
		?>
        <input type='text' value='<?php echo esc_attr($post->ID); ?>' onclick='this.focus(); this.select()' readonly
               class='ma_discount_shortcode_input'/>
		<?php
	}

	public function ma_discount_show_product_after_sell_cbf(): void
	{
		global $post;
		$discount_show_number_of_day_after_sell = get_post_meta($post->ID, '_discount_show_number_of_day_after_sell', true);
		?>
        <div class="discount_show_after_sell">
            <table class="wrapper" width="100%">
                <tbody class="container">
                <tr class="template row">
                    <td width="20%"><label for="ma_discount_show_product_after_sell"><?php
							esc_html_e('Show Product', 'ma_discount'); ?></label></td>
                    <td width="80%">
                        <input <?php if ($discount_show_number_of_day_after_sell !== "") {
							echo esc_attr('checked');
						}
						?> type="checkbox" id="ma_discount_show_product_after_sell"
                           name="ma_discount_show_product_after_sell"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr <?php if ($discount_show_number_of_day_after_sell === "") {
					echo 'style="display: none;"';
				}
				?> id="ma_discount_show_product_after_sell_number_of_day" class="template row">
                    <td width="20%" id="ma_discount_show_product_td_1">
                        <label for="ma_discount_show_number_of_day_after_sell"><?php
							esc_html_e('Show Number of Day in Carousel', 'ma_discount'); ?></label>
                    </td>
                    <td width="80%" id="ma_discount_show_product_td_2">
                        <input value="<?php if ($discount_show_number_of_day_after_sell !== "") {
							echo esc_attr($discount_show_number_of_day_after_sell);
						}
						?>" style="max-width: 100%;width: inherit;" type="number" min="1"
                               id="ma_discount_show_number_of_day_after_sell"
                               name="ma_discount_show_number_of_day_after_sell"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php
	}

	public function ma_discount_generate_product_html_metabox_cbf(): void
	{

		global $post;
		$ma_discount_base_meta = unserialize(get_post_meta($post->ID, 'ma_discount_base_meta', true), ['allowed_classes' => false]);
		$ma_discount_product_id = 0;
		?>
        <input type='hidden' name='ma_discount_metabox_nonce_name'
               value='<?php echo esc_attr(wp_create_nonce(plugin_basename(__FILE__))); ?>'/>
        <div class="discount_product">
            <table class="wrapper" width="100%">
                <tbody class="container">
                <tr class="template row">
                    <td width="20%">
                        <label for="discount_product"><?php esc_html_e("Product", 'ma_discount'); ?></label>
                    </td>
                    <td width="80%">
                        <select style="max-width: 100%;width: inherit;" required aria-describedby="tagline-description"
                                name="discount_product" id="discount_product">
                            <option value=""> <?php echo esc_attr__("Please select one of the products!", "ma_discount"); ?> </option>
							<?php
							foreach (get_posts(['post_type' => 'product']) as $product) {
								?>
                                <option <?php if (isset($ma_discount_base_meta['discount_product'])) {
									if ((int)$ma_discount_base_meta['discount_product'] === (int)$product->ID) {
										echo esc_attr('selected');
										$ma_discount_product_id = $product->ID;
									}
								} ?>
                                        value="<?php echo esc_attr($product->ID); ?> "> <?php echo esc_attr($product->post_title); ?>
                                </option>
								<?php
							}
							?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr id="ma_discount_product_price" <?php if (!isset($ma_discount_base_meta['discount_product'])) {
					echo 'style="display: none;"';
				}
				?>>
                    <td width='20%'><?php esc_html_e('Product Price'); ?></td>
                    <td width='80%'>
                        <label id="ma_discount_display_amount" for='ma_discount_display_amount'><?php
							if ($ma_discount_product_id !== 0) {
								echo wc_price(get_post_meta($ma_discount_product_id, "_regular_price", true));
							} ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr id="ma_discount_product_image" <?php if (!isset($ma_discount_base_meta['discount_product'])) {
					echo 'style="display: none;"';
				}
				?>>
                    <td width='20%'><?php esc_html_e('Product Image', 'ma_discount'); ?></td>
                    <td width='80%'>
                        <label id="ma_discount_display_image" for='ma_discount_display_image'><?php
							if ($ma_discount_product_id !== 0) {
								$attached_file = get_post_meta($ma_discount_product_id, '_thumbnail_id', true);
								$_wp_attached_file = wp_get_upload_dir()["baseurl"] . '/' . get_post_meta($attached_file, '_wp_attached_file', true);
								echo '<img src="' . $_wp_attached_file . '" alt="Select a product" width="350" height="450">';
							} ?></label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php
	}

	public function ma_discount_generate_date_html_metabox_cbf(): void
	{
		global $post;
		$ma_discount_base_meta = unserialize(get_post_meta($post->ID, 'ma_discount_base_meta', true), ['allowed_classes' => false]);
		?>
        <div class="discount_date">
            <table class="wrapper" width="100%">
                <tbody class="container">
                <tr class="template row">
                    <td width="20%">
                        <label for="discount_from_date"><?php esc_html_e('Start Date Discount', 'ma_discount'); ?></label>
                    </td>
                    <td width="80%">
                        <input class="short hasDatepicker pdp-el" autocomplete="off" required style="width: 80%;"
                               type="text" name="discount_from_date"
                               id="discount_from_date"
                               value="<?php if (isset($ma_discount_base_meta['discount_from_date'])) {
							       echo esc_attr($ma_discount_base_meta['discount_from_date']);
						       } ?>"/>
                        <p class="description"><?php esc_html_e('The discount time starts from the moment of storage on the selected day!', 'ma_discount'); ?></p>
                        <p class="description"><?php esc_html_e('Example 1400/04/01', 'ma_discount'); ?></p>
                    </td>
                </tr>
                <tr class="template row">
                    <td width="20%">
                        <label for="discount_to_date"><?php esc_html_e("End Date Discount", "ma_discount"); ?></label>
                    </td>
                    <td width="80%">
                        <input class="short hasDatepicker pdp-el" autocomplete="off" required style="width: 80%;"
                               type="text" name="discount_to_date"
                               id="discount_to_date"
                               value="<?php if (isset($ma_discount_base_meta['discount_to_date'])) {
							       echo esc_attr($ma_discount_base_meta['discount_to_date']);
						       } ?>"/>
                        <p class="description"><?php esc_html_e('The end time of the discount is until hours 24 on the selected day!', 'ma_discount'); ?></p>
                        <p class="description"><?php esc_html_e('Example 1400/04/07', 'ma_discount'); ?></p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php
	}

	public function ma_discount_generate_percent_html_metabox_cbf(): void
	{
		global $post;
		$ma_discount_base_meta = unserialize(get_post_meta($post->ID, 'ma_discount_base_meta', true), ['allowed_classes' => false]);
		?>
        <div class="discount_percent">
            <table class="wrapper" width="100%">
                <tbody class="container">
                <tr class="template row">
                    <td width="20%">
                        <label for="discount_percent"><?php esc_html_e("Product Discount Percent", "ma_discount"); ?></label>
                    </td>
                    <td width="80%">
                        <input required style="width: 80%" type="number" max="100" min="1" name="discount_percent"
                               onclick='this.focus(); this.select()'
                               id="discount_percent"
                               value="<?php if (isset($ma_discount_base_meta['discount_percent'])) {
							       echo esc_attr($ma_discount_base_meta['discount_percent']);
						       } ?>"/> <span>&nbsp&nbsp&nbsp%</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php
	}

	public function ma_discount_save_post_meta_cbf($post_id, $post, $update)
	{

		global $wpdb;
//        if (!wp_verify_nonce(isset($_POST["ma_discount_metabox_nonce_name"]),)){
//            var_dump($post_id,'1');die;
//            return $post_id;
//        }

		if (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		if (!empty(get_post_meta($post_id, 'ma_discount_base_meta'))) {

			$ex_discount_from_date_date = explode('/', $_POST['discount_from_date']);
			$discount_from_date_time = jmktime('0', '0', '0', $ex_discount_from_date_date[1], $ex_discount_from_date_date[2], $ex_discount_from_date_date[0]);
			$ex_discount_to_date_date = explode('/', $_POST['discount_to_date']);
			$discount_to_date_time = jmktime('23', '59', '59', $ex_discount_to_date_date[1], $ex_discount_to_date_date[2], $ex_discount_to_date_date[0]);

			$ma_discount_show_number_of_day_after_sell = "";
			if (isset($_POST["ma_discount_show_product_after_sell"])) {
				$ma_discount_show_number_of_day_after_sell = $_POST["ma_discount_show_number_of_day_after_sell"];
				update_post_meta($post_id, '_discount_show_number_of_day_after_sell', $ma_discount_show_number_of_day_after_sell);
			} else {
				delete_post_meta($post_id, '_discount_show_number_of_day_after_sell');
			}
			$regular_price = (int)get_post_meta($_POST['discount_product'], '_regular_price', true);
			$price_or_sale_price = $regular_price;
			if ($discount_from_date_time < time()) {
				$price_or_sale_price = floor($regular_price - (($regular_price * ((int)$_POST['discount_percent'])) / 100));
			}

			update_post_meta($post_id, '_discount_product_id', $_POST['discount_product']);
			update_post_meta($post_id, '_discount_percent', $_POST['discount_percent']);
			update_post_meta($post_id, '_discount_from_date', $_POST['discount_from_date']);
			update_post_meta($post_id, '_discount_from_date_time', $discount_from_date_time);
			update_post_meta($post_id, '_discount_to_date', $_POST['discount_to_date']);
			update_post_meta($post_id, '_discount_to_date_time', $discount_to_date_time);
			update_post_meta($post_id, '_discount_price', $price_or_sale_price);// قیمت فروش
			update_post_meta($post_id, '_discount_sale_price', $price_or_sale_price);// قیمت فروش

			$data = ['post_parent' => $_POST['discount_product']]; // NULL value.
			$where = ['id' => $post_id]; // NULL value in WHERE clause.
			$wpdb->update($wpdb->prefix . 'posts', $data, $where); // Also works in this case.
//			$my_post = array(
//				'ID'          => $post_id,
//				'post_parent' => $_POST['discount_product'],
//				'post_status' => 'publish',
//				'meta_input'  => array(
//					'_discount_product_id'     => $_POST['discount_product'],
//					'_discount_percent'        => $_POST['discount_percent'],
//					'_discount_from_date'      => $_POST['discount_from_date'],
//					'_discount_from_date_time' => $discount_from_date_time,
//					'_discount_to_date'        => $_POST['discount_to_date'],
//					'_discount_to_date_time'   => $discount_to_date_time,
//					'_discount_price'          => $price_or_sale_price,
//					'_discount_sale_price'     => $price_or_sale_price,
//				),
//				'edit_date' => true,
//				'post_date' => gmdate( 'Y-m-d H:i:s', strtotime('today') ),
//				'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime('today') )
//			);
//
//
//        	wp_update_post($my_post, true );
//			if (is_wp_error($post_id)) {
//				$errors = $post_id->get_error_messages();
//				foreach ($errors as $error) {
//					echo $error;
//					die;
//				}
//			}
//
			$params = array(
				'discount_product' => $_POST['discount_product'],
				'discount_percent' => $_POST['discount_percent'],
				'discount_from_date' => $_POST['discount_from_date'],
				'discount_to_date' => $_POST['discount_to_date'],
				'discount_from_date_time' => $discount_from_date_time,
				'discount_to_date_time' => $discount_to_date_time,
				'discount_show_number_of_day_after_sell' => $ma_discount_show_number_of_day_after_sell,
				'discount_price' => $price_or_sale_price,
				'discount_sale_price' => $price_or_sale_price,
			);

			update_post_meta($post_id, 'ma_discount_base_meta', serialize($params));

		} else {
			$params = array(
				'post_type' => 'ma_discount',
			);
			add_post_meta($post_id, 'ma_discount_base_meta', serialize($params));
		}
	}

	/**
	 * @throws JsonException
	 */
	public function ma_discount_pre_save_post_meta_cbf($post_id, $post_data): void
	{

		if (wp_is_post_revision($post_id)) {
			return;
		}

		if ($post_data['post_type'] === 'ma_discount') {

			if (isset($_POST["ma_discount_show_product_after_sell"]) && $_POST["ma_discount_show_number_of_day_after_sell"] === "") {

				update_option('my_notifications', json_encode(array(
					'error',
					__('Error: This field is required! "Show Number of Day in Carousel"', 'ma_discount')
				), JSON_THROW_ON_ERROR));

				header('Location: ' . get_edit_post_link($post_id, 'redirect'));
				exit;
			}

			$ex_discount_from_date_date = explode('/', $_POST['discount_from_date']);
			$discount_from_date_time = jmktime('0', '0', '0', $ex_discount_from_date_date[1], $ex_discount_from_date_date[2], $ex_discount_from_date_date[0]);
			$ex_discount_to_date_date = explode('/', $_POST['discount_to_date']);
			$discount_to_date_time = jmktime('23', '59', '59', $ex_discount_to_date_date[1], $ex_discount_to_date_date[2], $ex_discount_to_date_date[0]);

			$discount_percent = (((($discount_to_date_time - $discount_from_date_time) / 60) / 60) / 24) * $_POST['discount_percent'];
			if ($discount_percent > 100) {

				update_option('my_notifications', json_encode(array(
					'error',
					__('Error: This discount reaches more than 100% discount after a while! It is impossible!', 'ma_discount')
				), JSON_THROW_ON_ERROR));

				header('Location: ' . get_edit_post_link($post_id, 'redirect'));
				exit;
			}

			foreach (get_posts(['post_type' => $post_data['post_type']]) as $ma_discount) {
				if ($_POST['discount_product'] === $ma_discount->post_parent) {
					$ma_discount_from_date_time = get_post_meta($ma_discount->ID, '_discount_from_date_time', true);
					$ma_discount_to_date_time = get_post_meta($ma_discount->ID, '_discount_to_date_time', true);
					if (($discount_from_date_time >= $ma_discount_from_date_time && $discount_from_date_time <= $ma_discount_to_date_time)
						||
						($discount_to_date_time >= $ma_discount_from_date_time && $discount_to_date_time <= $ma_discount_to_date_time)) {
						update_option('my_notifications', json_encode(array(
							'error',
							__('There exist discount "Discount ID = ' . $ma_discount->ID . '" for "Product Name = ' . get_the_title($_POST['discount_product']) . '" on the selected date.', 'ma_discount')
						), JSON_THROW_ON_ERROR));

						header('Location: ' . get_edit_post_link($post_id, 'redirect'));
						exit;
					}
				}
			}

			if ($discount_from_date_time > $discount_to_date_time) {

				update_option('my_notifications', json_encode(array(
					'error',
					__('Post Start Date Discount can\'t be elder End Date Discount.', 'ma_discount')
				), JSON_THROW_ON_ERROR));

				header('Location: ' . get_edit_post_link($post_id, 'redirect'));
				exit;
			}

			if ($discount_to_date_time < time()) {

				update_option('my_notifications', json_encode(array(
					'error',
					__('Post End Date Discount can\'t be less Current Date.', 'ma_discount')
				), JSON_THROW_ON_ERROR));

				header('Location: ' . get_edit_post_link($post_id, 'redirect'));
				exit;
			}

			if (strlen($post_data['post_title']) < 5) {

				update_option('my_notifications', json_encode(array(
					'error',
					__('Post title can\'t be less than 5 characters.', 'ma_discount')
				), JSON_THROW_ON_ERROR));

				header('Location: ' . get_edit_post_link($post_id, 'redirect'));
				exit;
			}

			if (strlen($_POST['discount_product']) === "") {

				update_option('my_notifications', json_encode(array(
					'error',
					__('Post Product can\'t be null.', 'ma_discount')
				), JSON_THROW_ON_ERROR));

				header('Location: ' . get_edit_post_link($post_id, 'redirect'));
				exit;
			}
		}
	}

	/**
	 * @throws JsonException
	 */
	public function ma_discount_my_notification_cbf()
	{
		$notifications = get_option('my_notifications');

		if (!empty($notifications)) {
			$notifications = json_decode($notifications, true, 512, JSON_THROW_ON_ERROR);
			#notifications[0] = (string) Type of notification: error, updated or update-nag
			#notifications[1] = (string) Message
			#notifications[2] = (boolean) is_dismissible?
			switch ($notifications[0]) {
				case 'error': # red
				case 'updated': # green
				case 'update-nag': # ?
					$class = $notifications[0];
					break;
				default:
					# Defaults to error just in case
					$class = 'error';
					break;
			}

			$is_dismissable = '';
			if (isset($notifications[2]) && $notifications[2] === true) {
				$is_dismissable = 'is_dismissable';
			}

			echo '<div class="' . $class . ' notice ' . $is_dismissable . '">';
			echo '<p>' . $notifications[1] . '</p>';
			echo '</div>';

			update_option('my_notifications', false);
		}
	}
}

$MAPOSTTYPE = new MAPOSTTYPE();
