<?php

function ask_openai($conversation_history, $isFirstMessage) {

    $options = get_option('midrocket_chatbot_gpt_options');
    $api_key = $options['api_key'];
    $initial_prompt = $options['initial_prompt'];

    $api_key = $api_key ? $api_key : '';
    $ch = curl_init('https://api.openai.com/v1/chat/completions');

    if ($isFirstMessage && !empty($initial_prompt)) {
        array_unshift($conversation_history, [
            'role' => 'system',
            'content' => '"'.$initial_prompt.'"'
        ]);
    }

    $data = [
        'model' => 'gpt-3.5-turbo',
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
        echo esc_html( $response['choices'][0]['message']['content'] );
    } else {
        echo 'Lo siento, no pude procesar tu solicitud.';
    }

    wp_die();
}
add_action( 'wp_ajax_handle_chatbot_conversation', 'handle_chatbot_conversation' );
add_action( 'wp_ajax_nopriv_handle_chatbot_conversation', 'handle_chatbot_conversation' );