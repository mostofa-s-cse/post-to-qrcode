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
	add_settings_field('pqrc_height',__('QR Code Height','post-to-qrcode'),'pqrc_display_height','general','pqrc_qrcode_section');
	add_settings_field('pqrc_width',__('QR Code width','post-to-qrcode'),'pqrc_display_width','general','pqrc_qrcode_section');

	register_setting('general', 'pqrc_height',array('sanitize_callback' => 'esc_attr'));
	register_setting('general', 'pqrc_width',array('sanitize_callback' => 'esc_attr'));
}


function pqrc_display_height() {
	$height = get_option('pqrc_height');
	printf("<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_height', 'pqrc_height', $height);
}
function pqrc_display_width() {
	$width = get_option('pqrc_width');
	printf("<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_width', 'pqrc_width', $width);
}

function pqrc_section_callback() {
	echo "<p>".__('Settings for Post To QR Plugin','post-to-qrcode')."</p>";
}


add_action("admin_init", "qrcode_settings_init");