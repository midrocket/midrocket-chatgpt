<?php

function ask_openai($conversation_history, $isFirstMessage, $api_key_override = null) {

    $options            = get_option('midrocket_chatbot_gpt_options');

    $api_key            = $api_key_override ?? $options['api_key'] ?? '';
    $rules_prompt       = !empty($options['rules_prompt']) ? $options['rules_prompt'] : RULES_PROMPT;
    $gpt_model          = !empty($options['gpt_model']) ? $options['gpt_model'] : 'gpt-3.5-turbo';

    $ch = curl_init('https://api.openai.com/v1/chat/completions');

    if ($isFirstMessage && !empty($rules_prompt)) {

        if (!empty($options['knowledge']) && is_array($options['knowledge'])) {
            foreach ($options['knowledge'] as $knowledge_pair) {
                if (!empty($knowledge_pair['question']) && !empty($knowledge_pair['answer'])) {
                    array_unshift($conversation_history, [
                        'role' => 'assistant',
                        'content' => $knowledge_pair['answer']
                    ]);
                    array_unshift($conversation_history, [
                        'role' => 'user',
                        'content' => $knowledge_pair['question']
                    ]);
                }
            }
        }

        // Filter: Modify existing $rules_prompt
        $rules_prompt = apply_filters('chatbotgpt_filter_rules_prompt', $rules_prompt);

        array_unshift($conversation_history, [
            'role' => 'system',
            'content' => $rules_prompt
        ]);

    }

    $data = [
        'model' => $gpt_model,
        'messages' => $conversation_history,
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ];

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    if (!$result) {
        return ['error' => 'Failed to connect to OpenAI API'];
    }

    $response = json_decode($result, true);

    // Filter: Check answer and filter
    $response = apply_filters('chatbotgpt_filter_response_before', $response);

    return $response;
}

function handle_chatbot_conversation() {
    if ( ! isset( $_POST['message'] ) ) {
        echo 'No message received.';
        wp_die();
    }

    $conversation_history = json_decode( stripslashes( $_POST['message'] ), true );
    $isFirstMessage = isset($_POST['isFirstMessage']) ? (bool)$_POST['isFirstMessage'] : false;
    $response = ask_openai( $conversation_history, $isFirstMessage );

    // Suponiendo que $response contiene la respuesta formateada de OpenAI
    if ( isset( $response['choices'][0]['message']['content'] ) ) {

        // AMAZON: IF nos devuelve un formato de respuesta establecido en reglas que es tipo "keyword" esta incluido aqui hacemos otro ask_openai
        // add_filter('chatgpt_filter_response_before')
        // echo esc_html( $response['choices'][0]['message']['content'] );
        echo $response['choices'][0]['message']['content'];
    } else {
        print_r($response);
        echo 'Lo siento, no pude procesar tu solicitud.';
    }

    wp_die();
}
add_action( 'wp_ajax_handle_chatbot_conversation', 'handle_chatbot_conversation' );
add_action( 'wp_ajax_nopriv_handle_chatbot_conversation', 'handle_chatbot_conversation' );

function verify_openai_api_key() {
    if (!current_user_can('manage_options') || !isset($_POST['api_key'])) {
        wp_send_json_error(['message' => 'Unauthorized']);
        return;
    }

    $api_key = sanitize_text_field($_POST['api_key']);

    // Preparar un mensaje de prueba
    $conversation_history = [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => 'Hello, who are you?'],
    ];

    $response   = ask_openai($conversation_history, true, $api_key);

    if (!isset($response['error']) && isset($response['choices'])) {
        wp_send_json_success(['message' => 'API Key is valid.', 'options' => $options]);
    } else {
        wp_send_json_error(['message' => 'API Key is invalid.']);
    }
}
add_action('wp_ajax_verify_openai_api_key', 'verify_openai_api_key');
