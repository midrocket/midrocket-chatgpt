<?php
function chatbot_load() {
    
    $options            = get_option('midrocket_chatbot_gpt_options');
    $autoload_footer    = !empty($options['autoload_footer']) ? true : null;
    if($autoload_footer){
        chatbot_html_box($autoload_footer);
    }
    
}

function chatbot_custom_css() {
    
    $options            = get_option('midrocket_chatbot_gpt_options');
    $custom_css         = !empty($options['custom_css']) ? $options['custom_css'] : null;
    if($custom_css){
        echo '<style>'.$custom_css.'</style>';
    }
    
}

function chatbot_html_box($autoload = false){
    global $chatbot_loaded;

    if(is_admin()){
        return;
    }

    if ($chatbot_loaded) {
        return '';
    }
    
    $chatbot_loaded = true;

    $options            = get_option('midrocket_chatbot_gpt_options');

    $api_key            = $options['api_key'] ?? null;
    $gpt_model          = !empty($options['gpt_model']) ? $options['gpt_model'] : null;
    $rules_prompt       = !empty($options['rules_prompt']) ? $options['rules_prompt'] : null;

    // Style
    $bot_name           = !empty($options['bot_title']) ? $options['bot_title'] : 'Chatbot';
    $intro_message      = !empty($options['intro_message']) ? $options['intro_message'] : INTRO_MSG_NOT_PROMPT;
    $send_button        = !empty($options['send_button']) ? $options['send_button'] : __('Send', 'midrocket-chatgpt');
    $message_placeholder = !empty($options['message_placeholder']) ? $options['message_placeholder'] : __('Write your message here...', 'midrocket-chatgpt');
    $opened_by_default  = !empty($options['opened_by_default']) ? 'default-opened' : 'status-closed';
    $default_mode       = !empty($options['default_mode']) ? $options['default_mode'] : 'light';
    $dark_mode_toggle   = !empty($options['dark_mode_toggle']) ? $options['dark_mode_toggle'] : null;
    $icon               = !empty($options['icon']) ? '<div class="custom-icon"><img src="'.$options['icon'].'"></div>' : '<i class="fi fi-rr-chatbot-speech-bubble"></i>';
    $button_color       = !empty($options['button_color']) ? $options['button_color'] : null;
    $button_background_color = !empty($options['button_background_color']) ? $options['button_background_color'] : null;
    $header_color       = !empty($options['header_color']) ? $options['header_color'] : null;
    $header_background_color = !empty($options['header_background_color']) ? $options['header_background_color'] : null;

    $position           = !empty($options['position']) ? $options['position'] : 'bottom_right';

    if($button_color){
        echo "<style>:root { --chatbot-button-color: $button_color!important;}</style>";
    }
    if($button_background_color){
        echo "<style>:root { --chatbot-button-bg-color: $button_background_color!important;}</style>";
    }
    if($header_color){
        echo "<style>:root { --chatbot-header-color: $header_color!important;}</style>";
    }
    if($header_background_color){
        echo "<style>:root { --chatbot-header-bg-color: $header_background_color!important;}</style>";
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

    $css_class = '';
    if(!$autoload){
        $css_class = 'shortcode-chat';
        ob_start();
    }
    ?>
    <div class="chatbot-opener mode-<?php echo $default_mode; ?> <?php echo ($opened_by_default === 'status-closed') ? '' : 'status-closed'; ?> <?php echo $css_class; ?>" id="chatbot-bubble">
        <div class="chatbot-bubble-inner">
            <div class="chatbot-icon"><?php echo $icon; ?></div>
            <div class="chatbot-name"><?php echo $bot_name; ?></div>
        </div>
    </div>

    <div id="chatbot-container" class="position-<?php echo $position; ?> mode-<?php echo $default_mode; ?> <?php echo $opened_by_default; ?> <?php echo $css_class; ?>">
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
            <div id="chatbot-intro">
            <?php 
            if(empty($error)){
                echo '<div class="chatbot-icon">'.$icon.'</div>';
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
            <input type="text" id="chatbot-input" placeholder="<?php echo $message_placeholder; ?>" />
            <button id="chatbot-send-btn" disabled><?php echo $send_button; ?></button>
        </div>
        <?php } ?>
    </div>
    <?php
    if(!$autoload){
        $html = ob_get_clean();
        return $html;
    }
}

add_action('wp_footer', 'chatbot_load');
add_action('wp_footer', 'chatbot_custom_css');
add_shortcode('chatbot', 'chatbot_html_box' );
