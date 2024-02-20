<?php

function ask_openai($conversation_history, $isFirstMessage) {

    $options            = get_option('midrocket_chatbot_gpt_options');

    $api_key            = $options['api_key'] ?? '';
    $rules_prompt       = !empty($options['rules_prompt']) ? $options['rules_prompt'] : RULES_PROMPT;
    $gpt_model          = !empty($options['gpt_model']) ? $options['gpt_model'] : 'gpt-3.5-turbo';

    $ch = curl_init('https://api.openai.com/v1/chat/completions');

    if ($isFirstMessage && !empty($rules_prompt)) {

        if (!empty($options['knowledge']) && is_array($options['knowledge'])) {
            foreach ($options['knowledge'] as $knowledge_pair) {
                if (!empty($knowledge_pair['question']) && !empty($knowledge_pair['answer'])) {
                    array_unshift($conversation_history, [
                        'role' => 'user',
                        'content' => $knowledge_pair['question']
                    ]);
                    array_unshift($conversation_history, [
                        'role' => 'assistant',
                        'content' => $knowledge_pair['answer']
                    ]);
                }
            }
        }

        // AMAZON: include more rules directly from amazon plugin
        // add_filter('chatgpt_filter_rules_prompt', $rules_prompt)
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
        echo esc_html( $response['choices'][0]['message']['content'] );
    } else {
        print_r($response);
        echo 'Lo siento, no pude procesar tu solicitud.';
    }

    wp_die();
}
add_action( 'wp_ajax_handle_chatbot_conversation', 'handle_chatbot_conversation' );
add_action( 'wp_ajax_nopriv_handle_chatbot_conversation', 'handle_chatbot_conversation' );