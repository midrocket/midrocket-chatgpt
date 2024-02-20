<?php
function chatbot_add_footer_html() {

    $options            = get_option('midrocket_chatbot_gpt_options');

    $api_key            = $options['api_key'] ?? null;
    $gpt_model          = !empty($options['gpt_model']) ? $options['gpt_model'] : null;
    $rules_prompt       = !empty($options['rules_prompt']) ? $options['rules_prompt'] : null;
    $intro_message      = !empty($options['intro_message']) ? $options['intro_message'] : INTRO_MSG_NOT_PROMPT;
    $error = '';
    if(!$api_key){
        $error = 'Error: API Key not defined';
    }
    if(!$gpt_model){
        $error = 'Error: GPT Model not defined';
    }
    if(!$rules_prompt){
        $error = 'Error: System prompt not defined';
    }
    ?>
    <div id="chatbot-container">
        <div id="chatbot-messages">
            <div id="chatbot-intro">
            <?php 
            if(empty($error)){
                echo '<i class="fi fi-rr-chatbot-speech-bubble"></i>';
                echo $intro_message;
            }else{
                echo $error; 
            }
            ?>
            </div>
        </div>
        <?php if(empty($error)){ ?>
            <div class="chatbot-actions">
            <div id="chatbot-loading"><div class="spinner"><div class="shape"><div></div></div></div></div>
            <input type="text" id="chatbot-input" placeholder="<?php echo __('Write your message here...', 'midrocket-chatgpt'); ?>" />
            <button id="chatbot-send-btn" disabled><?php echo __('Send', 'midrocket-chatgpt'); ?></button>
        </div>
        <?php } ?>
    </div>
    <?php
}
add_action('wp_footer', 'chatbot_add_footer_html');
