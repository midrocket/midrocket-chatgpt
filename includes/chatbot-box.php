<?php
function chatbot_add_footer_html() {
    ?>
    <div id="chatbot-container">
        <div id="chatbot-messages">
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
