<?php
/**
 * Plugin Name: ChatGPT for Wordpress
 * Plugin URI: https://www.midrocket.com
 * Description: ChatGPT integration for Wordpress.
 * Version: 1.0
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
require_once plugin_dir_path( __FILE__ ) . 'includes/openai-integration.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/chatbot-box.php';

if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-settings.php';
}

function chatgpt_enqueue_scripts() {
    // Styles
    $style = plugins_url( 'assets/css/style.css', __FILE__ );
    wp_enqueue_style( 'chatgpt-style', $style, array(), get_cgpt_plugin_version(), 'all' );
    // Scripts
    wp_enqueue_script('chatbot-ajax-script', plugins_url('assets/js/chatbot-ajax.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('chatbot-ajax-script', 'chatbotAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action( 'wp_enqueue_scripts', 'chatgpt_enqueue_scripts' );

function chatgpt_enqueue_admin_style() {
    // Styles
    $style = plugins_url( 'assets/css/admin.css', __FILE__ );
    wp_enqueue_style( 'chatgpt-admin-style', $style, array(), get_cgpt_plugin_version(), 'all' );
    // Scripts
    wp_enqueue_script('chatgpt-admin-script', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'chatgpt_enqueue_admin_style');