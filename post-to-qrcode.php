<?php
/*
 * Plugin Name:       Post To QrCode
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics Word Count plugin.
 * Version:           1.0.
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mostofa
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       post-to-qrcode
 * Domain Path:       /languages
 */

$pqrc_countries = array(
	__('Afghanistan','post-to-qrcode'),
	__('Bangladesh','post-to-qrcode'),
	__('India','post-to-qrcode'),
	__('Maldives','post-to-qrcode'),
	__('Nepal','post-to-qrcode'),
);
function pqrc_init() {
	global $pqrc_countries;
	$pqrc_countries = apply_filters('pqrc_countries', $pqrc_countries);
}

add_action("init","pqrc_init");

function post_to_qrcode_load_textdomain() {
	load_plugin_textdomain('posts-to-qrcode', false, dirname(__FILE__) . '/languages');
}


function pqrc_display_qr_code($content) {
	$current_post_id = get_the_ID();
	$current_post_title = get_the_title($current_post_id);
	$current_post_url = urlencode(get_permalink($current_post_id));
	$current_post_type = get_post_type($current_post_id);
	/**
	 * Post Type Check
	 */
	$excluded_post_types = apply_filters('pqrc_excluded_post_types', array());
	if( in_array($current_post_type, $excluded_post_types)) {
		return $content;
	}

	/**
	 * Dimension Hook
	 */
	$height = get_option('pqrc_height');
	$width = get_option('pqrc_width');
	$height = $height?$height :180;
	$width = $width?$width :180;
	$dimension = apply_filters('pqrc_qrcode_dimension', "{$width}x{$height}");

	/**
	 * Image Attributes
	 */

	$image_attributes = apply_filters('pqrc_qrcode_image_attributes', null);

	$image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s',$dimension, $current_post_url);
	$qr_code_html = sprintf("<div class='qrcode'><img %s src='%s' alt='%s'/></div>",$image_attributes, $image_src, $current_post_title);

	// Append the QR code to the content
	$content .= $qr_code_html;

	return $content;
}
add_filter('the_content', 'pqrc_display_qr_code');



/**
 * themes.custom type and size
 */

function philosophy_excluded_post_types($post_types) {
	$post_types []= 'page';
//	array_push($post_types, 'post');
	return $post_types;
}
add_filter('pqrc_excluded_post_types','philosophy_excluded_post_types');



//function philosophy_qrcode_dimension($dimension) {
//	$dimension = '200x200';
//	return $dimension;
//}
//add_filter('pqrc_qrcode_dimension','philosophy_qrcode_dimension');



// wp-admin setting add filed setting menu general page.
function qrcode_settings_init() {
	//add_settings_section
	add_settings_section('pqrc_qrcode_section', __('QR Code General Settings'), 'pqrc_section_callback', 'general');
	//add_settings_field
	add_settings_field('pqrc_height',__('QR Code Height','post-to-qrcode'),'pqrc_display_field','general','pqrc_qrcode_section',array('pqrc_height'));
	add_settings_field('pqrc_width',__('QR Code Width','post-to-qrcode'),'pqrc_display_field','general','pqrc_qrcode_section',array('pqrc_width'));
	//dropdown...
	add_settings_field('pqrc_select',__('Extra Select/Dropdown Field','post-to-qrcode'),'pqrc_display_select_field','general','pqrc_qrcode_section');
	//checkbox.
	add_settings_field('pqrc_checkbox',__('Select Countries','post-to-qrcode'),'pqrc_display_checkbox_group_field','general','pqrc_qrcode_section');
	add_settings_field('pqrc_toggle',__('Toggle field','post-to-qrcode'),'pqrc_display_toggle_field','general','pqrc_qrcode_section');



	register_setting('general', 'pqrc_height',array('sanitize_callback' => 'esc_attr'));
	register_setting('general', 'pqrc_width',array('sanitize_callback' => 'esc_attr'));
	register_setting('general', 'pqrc_select',array('sanitize_callback' => 'esc_attr'));
	register_setting('general', 'pqrc_checkbox');
	register_setting('general', 'pqrc_toggle');
}


//function pqrc_display_height() {
//	$height = get_option('pqrc_height');
//	printf("<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_height', 'pqrc_height', $height);
//}

//function pqrc_display_width() {
//	$width = get_option('pqrc_width');
//	printf("<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_width', 'pqrc_width', $width);
//}


function pqrc_display_field($args) {
	$options = get_option($args[0]);
	printf("<input type='text' id='%s' name='%s' value='%s' />",$args[0],$args[0],$options);
}

function pqrc_section_callback() {
	echo "<p>".__('Settings for Post To QR Plugin','post-to-qrcode')."</p>";
}

//dropdown ...................
function pqrc_display_select_field() {
	global $pqrc_countries;
	$option = get_option('pqrc_select');
	printf("<select id='%s' name='%s'>",'pqrc_select','pqrc_select');
	foreach ($pqrc_countries as $country) {
		$selected = '';
		if($option == $country) {
			$selected = "selected";
		}
		printf("<option value='%s' %s>%s</option>",$country,$selected,$country);
	}
	echo "</select>";
}

//checkbox
function pqrc_display_checkbox_group_field() {
	global $pqrc_countries;
	$option = get_option('pqrc_checkbox');
	foreach ($pqrc_countries as $country) {
		$selected = '';
		if(is_array($option) && in_array($country,$option)){
			$selected = "checked";
		}
		printf("<input type='checkbox' name='pqrc_checkbox[]' value='%s' %s/> %s <br>",$country,$selected,$country);
	}
}




add_action("admin_init", "qrcode_settings_init");


function philosophy_settings_country_list($countries) {
	array_push($countries, 'Spain');

//	remove country
//	$countries = array_diff($countries, array('Bangladesh','India'));

	return $countries;
}
add_filter('pqrc_countries', 'philosophy_settings_country_list');


function pqrc_assets($screen) {
	if('options-general.php' == $screen) {
		wp_enqueue_style("minitoggle-css", plugin_dir_url(__FILE__) . 'assets/css/minitoggle.css');
		wp_enqueue_script("minitoggle-js", plugin_dir_url(__FILE__) . 'assets/js/minitoggle.js', array('jquery'),"1.0",true);
		wp_enqueue_script("pqrc-main-js", plugin_dir_url(__FILE__) . 'assets/js/pqrc-main.js', array('jquery'),time(),true);
	}
}
add_action("admin_enqueue_scripts",'pqrc_assets');






function pqrc_display_toggle_field() {
	echo  '<div class="toggle"></div>';
}





function philosophy_button( $attributes ) {

	$default = array(
		'type'=>'primary',
		'title'=>__("Button",'philosophy'),
		'url'=>'',
	);

	$button_attributes = shortcode_atts($default,$attributes);


	return sprintf( '<a target="_blank" class="btn btn--%s full-width" href="%s">%s</a>',
		$button_attributes['type'],
		$button_attributes['url'],
		$button_attributes['title']
	);
}

add_shortcode( 'button', 'philosophy_button' );


function philosophy_button2( $attributes, $content='' ) {
	$default = array(
		'type'=>'primary',
		'title'=>__("Button",'philosophy'),
		'url'=>'',
	);

	$button_attributes = shortcode_atts($default,$attributes);


	return sprintf( '<a target="_blank" class="btn btn--%s full-width" href="%s">%s</a>',
		$button_attributes['type'],
		$button_attributes['url'],
		do_shortcode($content)
	);
}

add_shortcode( 'button2', 'philosophy_button2' );



function philosophy_uppercase($attributes, $content='') {
	return strtoupper(do_shortcode($content));
}

add_shortcode('uc', 'philosophy_uppercase');


function philosophy_google_map($attributes){
	$default = array(
		'place'=>'Dhaka Museum',
		'width'=>'800',
		'height'=>'500',
		'zoom'=>'14'
	);

	$params = shortcode_atts($default,$attributes);

	$map = <<<EOD
	<div>
	    <div>
	        <iframe width="{$params['width']}" height="{$params['height']}"
	                src="https://maps.google.com/maps?q={$params['place']}&t=&z={$params['zoom']}&ie=UTF8&iwloc=&output=embed"
	                frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
	        </iframe>
	    </div>
	</div>
EOD;

	return $map;

}
add_shortcode('gmap','philosophy_google_map');