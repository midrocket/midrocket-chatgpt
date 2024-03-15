<?php
function chatbot_add_footer_html() {

    $options            = get_option('midrocket_chatbot_gpt_options');

    $api_key            = $options['api_key'] ?? null;
    $gpt_model          = !empty($options['gpt_model']) ? $options['gpt_model'] : null;
    $rules_prompt       = !empty($options['rules_prompt']) ? $options['rules_prompt'] : null;

    // Style
    // @TODO: opened_by_default
    $bot_name           = !empty($options['bot_title']) ? $options['bot_title'] : 'Chatbot';
    $opened_by_default  = !empty($options['opened_by_default']) ? 'default-opened' : 'status-closed';
    $default_mode       = !empty($options['default_mode']) ? $options['default_mode'] : 'light';
    $dark_mode_toggle   = !empty($options['dark_mode_toggle']) ? $options['dark_mode_toggle'] : null;
    $intro_message      = !empty($options['intro_message']) ? $options['intro_message'] : INTRO_MSG_NOT_PROMPT;
    $icon               = !empty($options['icon']) ? '<div class="custom-icon"><img src="'.$options['icon'].'"></div>' : '<i class="fi fi-rr-chatbot-speech-bubble"></i>';
    $button_color       = !empty($options['button_color']) ? $options['button_color'] : null;
    $button_background_color = !empty($options['button_background_color']) ? $options['button_background_color'] : null;
    $header_color       = !empty($options['header_color']) ? $options['header_color'] : null;
    $header_background_color = !empty($options['header_background_color']) ? $options['header_background_color'] : null;

    $position           = !empty($options['position']) ? $options['position'] : 'bottom_right';

    if($button_color){
        echo "<style>:root { --chatbot-button-color: $button_color;}</style>";
    }
    if($button_background_color){
        echo "<style>:root { --chatbot-button-bg-color: $button_background_color;}</style>";
    }
    if($header_color){
        echo "<style>:root { --chatbot-header-color: $header_color;}</style>";
    }
    if($header_background_color){
        echo "<style>:root { --chatbot-header-bg-color: $header_background_color;}</style>";
    }

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

    <?php // if($opened_by_default === 'status-closed'){ ?>
        <div class="chatbot-opener mode-<?php echo $default_mode; ?> <?php echo ($opened_by_default === 'status-closed') ? '' : 'status-closed'; ?> " id="chatbot-bubble">
            <div class="chatbot-bubble-inner">
                <div class="chatbot-icon"><?php echo $icon; ?></div>
                <div class="chatbot-name"><?php echo $bot_name; ?></div>
            </div>
        </div>
    <?php // } ?>

    <div id="chatbot-container" class="position-<?php echo $position; ?> mode-<?php echo $default_mode; ?> <?php echo $opened_by_default; ?>">
        <div id="chatbot-header">
            <div class="chatbot-name"><?php echo $bot_name; ?></div>
            <div class="chatbot-head-actions">
                <?php if($dark_mode_toggle){ ?>
                    <div class="chatbot-darkmode"><i class="fi fi-bs-moon"></i></div>
                <?php } ?>
                <div class="chatbot-close-btn chatbot-opener"><i class="fi fi-br-cross-small"></i></div>
            </div>
        </div>
        <div id="chatbot-messages">
        <?php 
            // Delete this clause + else and closing (only testing purposes)
            if(isset($_GET['super'])){
                echo '<pre>'.print_r(amazon_pa_api_search_products('taza de te')).'</pre>'; 
            }else{
        ?>
            <div id="chatbot-intro">
            <?php 
            if(empty($error)){
                echo '<div class="chatbot-icon">'.$icon.'</div>';
                echo $intro_message;
            }else{
                echo $error; 
            }
            ?>
        <?php } ?>
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

// Dev purposes: limit only logged in users
//function chatbot_conditional_footer_html() {
//    if ( is_user_logged_in() && current_user_can('manage_options') ) {
//        add_action('wp_footer', 'chatbot_add_footer_html');
//    }
//}
//add_action('init', 'chatbot_conditional_footer_html');

add_action('wp_footer', 'chatbot_add_footer_html');
