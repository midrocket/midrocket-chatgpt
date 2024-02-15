<?php
function chatbot_add_footer_html() {

    $options            = get_option('midrocket_chatbot_gpt_options');

    $company_name       = $options['company_name'];
    $intro_message       = !empty($options['intro_message']) ? $options['intro_message'] : INTRO_MSG_NOT_PROMPT;
    ?>
    <div id="chatbot-container">
        <div id="chatbot-messages">
            <div id="chatbot-intro">
            <i class="fi fi-rr-chatbot-speech-bubble"></i>
            <?php  echo str_replace("[COMPANY_NAME]", $company_name, $intro_message); ?>
            </div>
        </div>
        <div class="chatbot-actions">
            <div id="chatbot-loading"><div class="spinner"><div class="shape"><div></div></div></div></div>
            <input type="text" id="chatbot-input" placeholder="Escribe tu mensaje aquí..." />
            <button id="chatbot-send-btn">Enviar</button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'chatbot_add_footer_html');
