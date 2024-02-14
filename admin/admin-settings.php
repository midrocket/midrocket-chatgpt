<?php

function midrocket_chatbot_gpt_add_admin_menu() {
    add_menu_page(
        'Chatbot GPT Settings',
        'Chatbot Settings',
        'manage_options',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_page'
    );
}
add_action('admin_menu', 'midrocket_chatbot_gpt_add_admin_menu');

function midrocket_chatbot_gpt_settings_page() {
    ?>
    <div class="wrap">
        <h2>Chatbot GPT Settings</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('midrocket_chatbot_gpt_options_group');
            do_settings_sections('midrocket_chatbot_gpt_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function midrocket_chatbot_gpt_settings_init() {
    register_setting('midrocket_chatbot_gpt_options_group', 'midrocket_chatbot_gpt_options', 'midrocket_chatbot_gpt_options_validate');

    add_settings_section(
        'midrocket_chatbot_gpt_settings_section',
        'Chatbot GPT Settings',
        'midrocket_chatbot_gpt_settings_section_callback',
        'midrocket_chatbot_gpt_settings'
    );

    add_settings_field(
        'midrocket_chatbot_gpt_api_key',
        'OpenAI API Key',
        'midrocket_chatbot_gpt_api_key_render',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_section'
    );

    add_settings_field(
        'midrocket_chatbot_gpt_initial_prompt',
        'Initial Prompt',
        'midrocket_chatbot_gpt_initial_prompt_render',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_section'
    );
}
add_action('admin_init', 'midrocket_chatbot_gpt_settings_init');

function midrocket_chatbot_gpt_settings_section_callback() {
    echo 'Enter your settings below:';
}

function midrocket_chatbot_gpt_api_key_render() {
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <input type='text' name='midrocket_chatbot_gpt_options[api_key]' value='<?php echo $options['api_key']; ?>'>
    <?php
}

function midrocket_chatbot_gpt_initial_prompt_render() {
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <textarea name='midrocket_chatbot_gpt_options[initial_prompt]' rows='5' cols='50'><?php echo $options['initial_prompt']; ?></textarea>
    <?php
}

function midrocket_chatbot_gpt_options_validate($input) {
    $new_input['api_key'] = sanitize_text_field($input['api_key']);
    $new_input['initial_prompt'] = sanitize_textarea_field($input['initial_prompt']);
    return $new_input;
}
