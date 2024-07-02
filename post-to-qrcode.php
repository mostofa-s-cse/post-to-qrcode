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

	$dimension = apply_filters('pqrc_qrcode_dimension', '150x150');

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



function philosophy_qrcode_dimension($dimension) {
	$dimension = '200x200';
	return $dimension;
}
add_filter('pqrc_qrcode_dimension','philosophy_qrcode_dimension');