<?php
/**
 * create settings section field
 */
function ma_discount_settings_init_cbf()
{
	register_setting(MA_DISCOUNT_PLUGIN_NAME, 'ma_discount_opts', 'handle_file_upload');
	$opts = get_option('ma_discount_opts');

	add_settings_section(
		'settings-sections',
		__('Settings Discount', 'ma_discount'),
		'ma_discount_settings_section_description_cbf',
		MA_DISCOUNT_PLUGIN_NAME
	);
	add_settings_field(
		'user',
		__('User', 'ma_discount'),
		'ma_discount_settings_user_cbf',
		MA_DISCOUNT_PLUGIN_NAME,
		'settings-sections',
		[
			'name' => 'user_id',
			'label_for' => 'ma_discount_settings_user',
			'class' => 'ma_discount_settings_user',
			'options' => $opts
		]
	);
	add_settings_field(
		'carousel_image',
		__('Carousel Image', 'ma_discount'),
		'ma_discount_settings_carousel_image_cbf',
		MA_DISCOUNT_PLUGIN_NAME,
		'settings-sections',
		[
			'name' => 'carousel_image',
			'label_for' => 'ma_discount_settings_carousel_image',
			'class' => 'ma_discount_settings_carousel_image',
			'options' => $opts
		]
	);
	add_settings_field(
		'shortcode',
		__('ShortCode', 'ma_discount'),
		'ma_discount_settings_shortcode_cbf',
		MA_DISCOUNT_PLUGIN_NAME,
		'settings-sections',
		[
			'name' => 'ShortCode',
			'label_for' => 'ma_discount_settings_shortcode',
			'class' => 'ma_discount_settings_shortcode',
			'options' => $opts
		]
	);
}

add_action('admin_init', 'ma_discount_settings_init_cbf');

/**
 * save file uploaded for carousel image
 *
 * @param $args
 *
 * @return mixed
 */
function handle_file_upload($args): mixed
{
	if (isset($_FILES["ma_discount_opts"]["name"]["carousel_image"]) && !empty($_FILES["ma_discount_opts"]["name"]["carousel_image"])) {

		$target_dir = MA_DISCOUNT_PLUGIN_PATH . 'assets/images/';
		$target_file = $target_dir . wp_basename($_FILES["ma_discount_opts"]["name"]["carousel_image"]);
		if (!empty(get_option('ma_discount_carousel_image'))) {
			unlink($target_dir . get_option('ma_discount_carousel_image'));
		}
		$check = getimagesize($_FILES["ma_discount_opts"]["tmp_name"]["carousel_image"]);
		if ($check !== false) {
			move_uploaded_file($_FILES["ma_discount_opts"]["tmp_name"]["carousel_image"], $target_file);
		}
		update_option('ma_discount_carousel_image', wp_basename($_FILES["ma_discount_opts"]["name"]["carousel_image"]));
	}

	return $args;
}

/**
 * Print the Section text
 */
function ma_discount_settings_section_description_cbf()
{
	_e('Select one of the users as a buyer after the expiration date of the discount which will be displayed in the front part of the site. Select a carousel image  to be displayed as a carousel when the discount is created. And short code that you can use anywhere on the site.', 'ma_discount');
}

/**
 * Get the settings option array and print one of its values
 */
function ma_discount_settings_user_cbf($args)
{
	$opts = $args['options'];
	$users = get_users();
	?>
    <label for="<?php echo esc_attr($args['label_for']); ?>"></label>
    <select style="width: 100%" name="ma_discount_opts[<?php echo esc_attr($args['name']); ?>]"
            id="<?php echo esc_attr($args['label_for']); ?>">
		<?php
		foreach ($users as $user) {
			?>
            <option <?php if (isset($opts["user_id"]) && $opts["user_id"] === $user->data->ID) {
				echo 'selected';
			} ?> value="<?php echo esc_attr($user->data->ID); ?>"><?php echo esc_html($user->data->display_name); ?></option>
			<?php
		}
		?>
    </select>
    <p class="description"><?php esc_html_e('Select the user to buy automatically after the end of the discount time', 'ma_discount'); ?></p>
	<?php
}

/**
 * Get the settings option array and print one of its values
 */
function ma_discount_settings_carousel_image_cbf($args)
{
	$img_dir = MA_DISCOUNT_PLUGIN_URL . 'assets/images/' . get_option('ma_discount_carousel_image');
	?>
    <input type="file" id="<?php echo esc_attr($args['label_for']); ?>"
           name="ma_discount_opts[<?php echo esc_attr($args['name']); ?>]"/>
    <p class="description"><?php esc_html_e('Select front carousel image', 'ma_discount'); ?></p>
	<?php
	if (!empty(get_option('ma_discount_carousel_image'))) {
		?>
        <br>
        <img src="<?php echo esc_attr($img_dir); ?>" width="300px" alt=""/>
		<?php
	}
}

/**
 * Get the settings option array and print one of its values
 */
function ma_discount_settings_shortcode_cbf($args)
{
	$opts = $args['options'];
	?>
    <input type="hidden" name="ma_discount_opts[<?php echo esc_attr($args['name']); ?>]"
           id="<?php echo esc_attr($args['label_for']); ?>"
           value="<h1>shortcode</h1>">
    <label><?php if (isset($opts[$args['name']])) {
        echo esc_html(($opts[$args['name']]));
    } ?>
    </label>
    <p class="description">
		<?php
		esc_html_e('You can place this shortcode anywhere on your site.', 'ma_discount');
		?>
    </p>
	<?php
}

/**
 * create settings form and submit button for save settings section data
 */
function ma_discount_settings_cbf()
{
	?>
    <div class="wrap ma_discount">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post" enctype="multipart/form-data">
			<?php
			settings_fields(MA_DISCOUNT_PLUGIN_NAME);
			do_settings_sections(MA_DISCOUNT_PLUGIN_NAME);
			submit_button(__('Save Settings', 'ma_discount'), MA_DISCOUNT_PLUGIN_NAME);
			?>
        </form>
    </div>
	<?php
}
