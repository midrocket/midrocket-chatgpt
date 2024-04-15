<?php
/**
 * Plugin Name: ChatGPT for Wordpress
 * Plugin URI: https://www.midrocket.com
 * Description: ChatGPT integration for Wordpress.
 * Version: 1.0.4
 * Author: Midrocket
 * Author URI: https://www.midrocket.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function get_cgpt_plugin_version(){
    $plugin_file = plugin_dir_path( __FILE__ ). 'midrocket-chatgpt.php';
    $plugin_data = get_file_data($plugin_file, array('Version' => 'Version'), 'plugin');
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/prompt-setup.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/openai-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/chatbot-html.php';

if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-settings.php';
}

function chatbotgpt_enqueue_scripts() {
    // Styles
    $style = plugins_url( 'assets/css/style.css', __FILE__ );
    wp_enqueue_style( 'chatbotgpt-style', $style, array(), get_cgpt_plugin_version(), 'all' );
    // Scripts
    wp_enqueue_script('chatbotgpt-ajax-script', plugins_url('assets/js/chatbot.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('chatbotgpt-ajax-script', 'chatbotAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action( 'wp_enqueue_scripts', 'chatbotgpt_enqueue_scripts' );

function chatbotgpt_enqueue_admin_style() {
    // Styles
    $style = plugins_url( 'assets/css/admin.css', __FILE__ );
    wp_enqueue_style( 'chatbotgpt-admin-style', $style, array(), get_cgpt_plugin_version(), 'all' );
    // Scripts
    wp_enqueue_script('chatbotgpt-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'chatbotgpt_enqueue_admin_style');


// AMAZON
// Move to external plugin
require_once plugin_dir_path( __FILE__ ) . 'includes/amazonpa-api.php';

function chatbotgpt_amazon_enqueue_scripts() {
    // Styles
    $style = plugins_url( 'assets/css/amazon.css', __FILE__ );
    wp_enqueue_style( 'chatbot-amazon-style', $style, array(), get_cgpt_plugin_version(), 'all' );
}
add_action( 'wp_enqueue_scripts', 'chatbotgpt_amazon_enqueue_scripts' );