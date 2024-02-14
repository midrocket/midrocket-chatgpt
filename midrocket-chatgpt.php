<?php
/**
 * Plugin Name: ChatGPT for Wordpress
 * Plugin URI: https://www.midrocket.com
 * Description: ChatGPT integration for Wordpress.
 * Version: 0.1
 * Author: Midrocket
 * Author URI: https://www.midrocket.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/openai-integration.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/chatbot-box.php';

if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-settings.php';
}

function chatgpt_enqueue_style() {
    $style = plugins_url( 'assets/css/style.css', __FILE__ );
    wp_enqueue_style( 'chatgpt-style', $style, array(), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'chatgpt_enqueue_style' );

function chatbot_enqueue_scripts() {
    wp_enqueue_script('chatbot-ajax-script', plugins_url('assets/js/chatbot-ajax.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('chatbot-ajax-script', 'chatbotAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'chatbot_enqueue_scripts');