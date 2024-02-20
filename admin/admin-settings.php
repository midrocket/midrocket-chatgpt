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
    <h2>Chatbot GPT</h2>
    <h2 class="nav-tab-wrapper">
        <a href="#api-settings" class="nav-tab nav-tab-active" id="api-settings-tab">API Settings</a>
        <a href="#main-settings" class="nav-tab" id="main-settings-tab">Main Settings</a>
        <a href="#knowledge-settings" class="nav-tab" id="main-settings-tab">Knowledge</a>
    </h2>
    <div id="api-settings" class="tab-content">
        <form action="options.php" method="post">
            <?php
            settings_fields('midrocket_chatbot_gpt_options_group');
            do_settings_sections('midrocket_chatbot_gpt_settings_api');
            submit_button();
            ?>
        </form>
    </div>
    <div id="main-settings" class="tab-content" style="display: none;">
        <form action="options.php" method="post">
            <?php
            settings_fields('midrocket_chatbot_gpt_options_group');
            do_settings_sections('midrocket_chatbot_gpt_settings_main');
            submit_button();
            ?>
        </form>
    </div>
    <div id="knowledge-settings" class="tab-content" style="display: none;">
        <form action="options.php" method="post">
            <?php
            settings_fields('midrocket_chatbot_gpt_options_group');
            do_settings_sections('midrocket_chatbot_gpt_settings_knowledge');
            submit_button();
            ?>
        </form>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('a.nav-tab').click(function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $('#' + $(this).attr('href').substring(1)).show();
        });
    });
</script>
<?php
}

function midrocket_chatbot_gpt_settings_init()
{
    register_setting('midrocket_chatbot_gpt_options_group', 'midrocket_chatbot_gpt_options', 'midrocket_chatbot_gpt_options_validate');

    add_settings_section(
        'midrocket_chatbot_gpt_settings_api_section',
        'API Settings',
        'midrocket_chatbot_gpt_api_settings_section_callback',
        'midrocket_chatbot_gpt_settings_api'
    );

    add_settings_field(
        'midrocket_chatbot_gpt_api_key',
        'OpenAI API Key',
        'midrocket_chatbot_gpt_api_key_render',
        'midrocket_chatbot_gpt_settings_api',
        'midrocket_chatbot_gpt_settings_api_section'
    );

    add_settings_section(
        'midrocket_chatbot_gpt_settings_main_section',
        'Main Settings',
        'midrocket_chatbot_gpt_main_settings_section_callback',
        'midrocket_chatbot_gpt_settings_main'
    );

    add_settings_field(
        'midrocket_chatbot_gpt_intro_message',
        'Intro message',
        'midrocket_chatbot_gpt_intro_message_render',
        'midrocket_chatbot_gpt_settings_main',
        'midrocket_chatbot_gpt_settings_main_section'
    );

    add_settings_field(
        'midrocket_chatbot_gpt_company_name',
        'Company Name',
        'midrocket_chatbot_gpt_company_name_render',
        'midrocket_chatbot_gpt_settings_main',
        'midrocket_chatbot_gpt_settings_main_section'
    );

    if(isset($_GET['super'])) {
        add_settings_field(
            'midrocket_chatbot_gpt_rules_prompt',
            'System Prompt',
            'midrocket_chatbot_gpt_rules_prompt_render',
            'midrocket_chatbot_gpt_settings_main',
            'midrocket_chatbot_gpt_settings_main_section'
        );
    }
    // add_settings_field(
    //     'midrocket_chatbot_gpt_specific_content',
    //     'Specific Content<p style="font-weight:normal">Paste all the raw content with the information ChatGPT should know (f.e. Full FAQ articles).</p>',
    //     'midrocket_chatbot_gpt_specific_content_render',
    //     'midrocket_chatbot_gpt_settings_main',
    //     'midrocket_chatbot_gpt_settings_main_section'
    // );
    // add_settings_field(
    //     'midrocket_chatbot_gpt_tematic_prompt',
    //     'Tematic Prompt<p style="font-weight:normal">Specify the tematic of the Chatbot, be as specific as you need to be.</p>',
    //     'midrocket_chatbot_gpt_tematic_prompt_render',
    //     'midrocket_chatbot_gpt_settings_main',
    //     'midrocket_chatbot_gpt_settings_main_section'
    // );

    add_settings_section(
        'midrocket_chatbot_gpt_settings_knowledge_a_section',
        'Automatic Knowledge',
        'midrocket_chatbot_gpt_knowledge_settings_a_section_callback',
        'midrocket_chatbot_gpt_settings_knowledge'
    );

    add_settings_field(
        'midrocket_chatbot_gpt_knowledge_automatic',
        'Generate Knowledge',
        'midrocket_chatbot_gpt_knowledge_automatic_render',
        'midrocket_chatbot_gpt_settings_knowledge',
        'midrocket_chatbot_gpt_settings_knowledge_a_section'
    );

    add_settings_section(
        'midrocket_chatbot_gpt_settings_knowledge_m_section',
        'Manage Knowledge',
        'midrocket_chatbot_gpt_knowledge_settings_m_section_callback',
        'midrocket_chatbot_gpt_settings_knowledge'
    );

}
add_action('admin_init', 'midrocket_chatbot_gpt_settings_init');

function midrocket_chatbot_gpt_api_settings_section_callback()
{
    echo '<div class="chatgpt-legend grey-box">
            <p>You can obtain your API Key from OpenAI under <a href="https://platform.openai.com/api-keys" target="_blank"><strong>API Keys</strong></a> <a href="https://platform.openai.com/api-keys" target="_blank" style="text-decoration: none"><i class="fi fi-rr-up-right-from-square"></i></a>.</p>
        </div>';
}

function midrocket_chatbot_gpt_main_settings_section_callback()
{
    echo '<div class="chatgpt-legend grey-box">
            <strong>Legend</strong>
            <p>You can use [COMPANY_NAME] under any prompt field to be replaced for the Company Name entered in the field above.</p>
        </div>';
}

// Knowledge
function midrocket_chatbot_gpt_knowledge_settings_a_section_callback()
{
    echo '<p>In this section you can generate all the questions and answers needed to your model from your internal pages content. Select the pagues you wish the model to take into consideration for Knowledge.</p>';
}

function midrocket_chatbot_gpt_knowledge_automatic_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <button type="button" class="button button-secondary">Start generating automatic Q&A</button>
<?php
}

function midrocket_chatbot_gpt_knowledge_settings_m_section_callback()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    $knowledge = isset($options['knowledge']) ? $options['knowledge'] : [];

    echo '<p>In this section you can add all the Knowledge to your model so the Chatbot already knows the answer to specified questions. Automatic generated knowledge will be shown here.</p>';
    ?>
    <div id="knowledge-repeater">
        <?php foreach ($knowledge as $index => $qa): ?>
            <div class="knowledge-pair" data-index="<?php echo $index; ?>">
                <div class="knowledge-summary">
                    <span class="knowledge-question-title"><?php echo esc_html($qa['question']); ?></span>
                    <div class="knowledge-actions">
                        <button type="button" class="button button-secondary button-remove remove-knowledge-pair">Remove</button>    
                        <button type="button" class="button button-secondary edit-knowledge-pair">Edit</button>
                    </div>
                </div>
                <div class="knowledge-edit-form" style="display: none;">
                    <input type="text" name="midrocket_chatbot_gpt_options[knowledge][<?php echo $index; ?>][question]" value="<?php echo esc_attr($qa['question']); ?>" placeholder="Question" />
                    <textarea name="midrocket_chatbot_gpt_options[knowledge][<?php echo $index; ?>][answer]" placeholder="Answer" rows="4"><?php echo esc_textarea($qa['answer']); ?></textarea>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="button" id="add-knowledge-pair" class="button button-secondary">Add New Question</button>
    </div>
    <?php
    
}

function midrocket_chatbot_gpt_api_key_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <input type='text' name='midrocket_chatbot_gpt_options[api_key]'
    value='<?php echo $options['api_key']; ?>'>
<?php
}

function midrocket_chatbot_gpt_intro_message_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <input type='text' class='long-input' name='midrocket_chatbot_gpt_options[intro_message]'
    value='<?php echo $options['intro_message']; ?>'
    placeholder="Hi, I'm JetSet Private Jets Chatbot! Do you have any questions?">
<?php
}

function midrocket_chatbot_gpt_company_name_render()
{
    $options = get_option('midrocket_chatbot_gpt_options');
    ?>
    <input type='text' class='long-input' name='midrocket_chatbot_gpt_options[company_name]'
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
    $current_options = get_option('midrocket_chatbot_gpt_options');

    if(isset($input['api_key'])) {
        $current_options['api_key'] = sanitize_text_field($input['api_key']);
    }
    if(isset($input['intro_message'])) {
        $current_options['intro_message'] = sanitize_text_field($input['intro_message']);
    }
    if(isset($input['company_name'])) {
        $current_options['company_name'] = sanitize_text_field($input['company_name']);
    }
    if(isset($input['specific_content'])) {
        $current_options['specific_content'] = sanitize_textarea_field($input['specific_content']);
    }
    if(isset($input['tematic_prompt'])) {
        $current_options['tematic_prompt'] = sanitize_textarea_field($input['tematic_prompt']);
    }
    if(isset($input['rules_prompt'])) {
        $current_options['rules_prompt'] = sanitize_textarea_field($input['rules_prompt']);
    }
    $current_options['knowledge'] = [];
    if (isset($input['knowledge']) && is_array($input['knowledge'])) {
        foreach ($input['knowledge'] as $index => $qa) {
            if (!empty($qa['question']) && !empty($qa['answer'])) {
                $current_options['knowledge'][] = [
                    'question' => sanitize_text_field($qa['question']),
                    'answer' => sanitize_textarea_field($qa['answer']),
                ];
            }
        }
    }

    return $current_options;
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