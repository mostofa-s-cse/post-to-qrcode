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
	$image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s', $current_post_url);
	$qr_code_html = sprintf("<div class='qrcode'><img src='%s' alt='%s'/></div>", $image_src, $current_post_title);

	// Append the QR code to the content
	$content .= $qr_code_html;

	return $content;
}
add_filter('the_content', 'pqrc_display_qr_code');
