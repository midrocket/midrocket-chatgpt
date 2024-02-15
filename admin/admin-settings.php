<?php

function midrocket_chatbot_gpt_add_admin_menu()
{
    add_menu_page(
        'Chatbot GPT Settings',
        'Chatbot Settings',
        'manage_options',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_page'
    );
}
add_action('admin_menu', 'midrocket_chatbot_gpt_add_admin_menu');

function midrocket_chatbot_gpt_settings_page()
{
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

function midrocket_chatbot_gpt_settings_init()
{
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
        'midrocket_chatbot_gpt_company_name',
        'Company Name',
        'midrocket_chatbot_gpt_company_name_render',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_section'
    );

    if(isset($_GET['super'])) {
        add_settings_field(
            'midrocket_chatbot_gpt_rules_prompt',
            'Rules Prompt',
            'midrocket_chatbot_gpt_rules_prompt_render',
            'midrocket_chatbot_gpt_settings',
            'midrocket_chatbot_gpt_settings_section'
        );
    }
    add_settings_field(
        'midrocket_chatbot_gpt_specific_content',
        'Specific Content<p style="font-weight:normal">Paste all the raw content with the information ChatGPT should know (f.e. Full FAQ articles).</p>',
        'midrocket_chatbot_gpt_specific_content_render',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_section'
    );
    add_settings_field(
        'midrocket_chatbot_gpt_tematic_prompt',
        'Tematic Prompt<p style="font-weight:normal">Specify the tematic of the Chatbot, be as specific as you need to be.</p>',
        'midrocket_chatbot_gpt_tematic_prompt_render',
        'midrocket_chatbot_gpt_settings',
        'midrocket_chatbot_gpt_settings_section'
    );
}
add_action('admin_init', 'midrocket_chatbot_gpt_settings_init');

function midrocket_chatbot_gpt_settings_section_callback()
{
    echo '<div class="chatgpt-legend grey-box">
            <strong>Legend</strong>
            <p>You can use [COMPANY_NAME] under any prompt field to be replaced for the Company Name entered in the field above.</p>
        </div>';
}

function midrocket_chatbot_gpt_api_key_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <input type='text' name='midrocket_chatbot_gpt_options[api_key]'
    value='<?php echo $options['api_key']; ?>'>
<?php
}

function midrocket_chatbot_gpt_company_name_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <input type='text' name='midrocket_chatbot_gpt_options[company_name]'
    value='<?php echo $options['company_name']; ?>'>
<?php
}

function midrocket_chatbot_gpt_rules_prompt_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <div class="input-example">
        <textarea name='midrocket_chatbot_gpt_options[rules_prompt]' rows='10'
        cols='75'><?php echo !empty($options['rules_prompt']) ? $options['rules_prompt'] : RULES_PROMPT; ?></textarea>
        <?php example_collapsible(RULES_PROMPT); ?>
    </div>
    <?php
}

function midrocket_chatbot_gpt_specific_content_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <div class="input-example">
        <textarea name='midrocket_chatbot_gpt_options[specific_content]' rows='10'
        cols='75' placeholder="Paste here all the content the chatbot should know to give answers"><?php echo $options['specific_content']; ?></textarea>
        <?php example_collapsible(SPECIFIC_CONTENT_EXAMPLE); ?>
    </div>
    <?php
}

function midrocket_chatbot_gpt_tematic_prompt_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <div class="input-example">
      <textarea name='midrocket_chatbot_gpt_options[tematic_prompt]' rows='10'
      cols='75'><?php echo !empty($options['tematic_prompt']) ? $options['tematic_prompt'] : '(Ejemplo actual) '.TEMATIC_PROMPT; ?></textarea>
      <?php example_collapsible(TEMATIC_PROMPT); ?>
    </div>
<?php
}

function midrocket_chatbot_gpt_options_validate($input)
{
    $new_input['api_key'] = sanitize_text_field($input['api_key']);
    $new_input['company_name'] = sanitize_text_field($input['company_name']);
    $new_input['specific_content'] = sanitize_textarea_field($input['specific_content']);
    $new_input['tematic_prompt'] = sanitize_textarea_field($input['tematic_prompt']);
    
    if(isset($input['rules_prompt'])) {
        $new_input['rules_prompt'] = sanitize_textarea_field($input['rules_prompt']);
    }
    return $new_input;
}

function example_collapsible($content, $example_title = null){
    ?>
    <div class="example">
        <div class="collapsible-title collapsible"><strong><?php echo $example_title ?? 'Show example'; ?></strong><i class="fi fi-rr-plus"></i><i class="fi fi-rr-minus"></i></div>
        <div class="collapsible-content">
            <strong>Example:</strong>
            <p><?php echo nl2br( $content ); ?></p>
        </div>
    </div>
    <?php
}
?>