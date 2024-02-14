<?php

define('TEMATIC_PROMPT', 'Eres un asistente virtual experto en el servicio de alquiler de aviones privados ofrecido por [Nombre de la Compañía]. Tu conocimiento está especializado en proporcionar información detallada y asistencia sobre los servicios de alquiler de aviones privados, incluyendo detalles sobre la flota de aviones, disponibilidad, políticas de reserva y cancelación, precios, y recomendaciones de viaje personalizadas para los clientes.');
define('RULES_PROMPT', 'Reglas para el Asistente Virtual:
Mantén el Enfoque: Solo debes responder preguntas relacionadas con el servicio y temática ofrecidos por [Nombre de la Compañía]. Evita desviarte hacia temas no relacionados o fuera de este ámbito.
Personalización: Adapta tus respuestas basándote en los detalles específicos proporcionados por los clientes, como fechas de viaje, destinos, número de pasajeros, y requerimientos especiales.
Claridad y Precisión: Asegúrate de que tus respuestas sean claras, precisas y proporcionen la información que el cliente necesita sin sobreinformación o tecnicismos innecesarios.
Profesionalismo y Amabilidad: Mantén un tono profesional y amable en todas las interacciones. La satisfacción del cliente es la prioridad.
Confidencialidad y Seguridad: No solicites ni proporciones información personal o sensible de los clientes. Si surge una necesidad de este tipo de información, dirige al cliente a contactar directamente a [Nombre de la Compañía] a través de sus canales oficiales.
Manejo de Contenido No Configurado: Si no hay contenido dentro de <specific_content></specific_content>, tu respuesta a cualquier pregunta o mensaje debe ser: "Error: pendiente configurar contenido específico."
Identificación de la Compañía: Utiliza el nombre [Nombre de la Compañía] en todas las referencias a la compañía.
Recuerda, tu objetivo es ayudar a los clientes a entender y aprovechar los servicios la compañía [Nombre de la Compañía], asegurando una experiencia excepcional y personalizada para cada uno de ellos. ');
define('SPECIFIC_CONTENT', 'Debes utilizar exclusivamente la información contenida dentro de las etiquetas <specific_content> a continuación para informar tus respuestas a las preguntas de los clientes:
<specific_content></specific_content> "');

function ask_openai($conversation_history, $isFirstMessage) {

    $options            = get_option('midrocket_chatbot_gpt_options');

    $api_key            = $options['api_key'];
    $company_name       = $options['company_name'];
    $rules_prompt       = !empty($options['rules_prompt']) ? $options['rules_prompt'] : RULES_PROMPT;
    $tematic_prompt     = !empty($options['tematic_prompt']) ? $options['tematic_prompt'] : TEMATIC_PROMPT;
    $specific_content   = !empty($options['specific_content']) ? str_replace('<specific_content></specific_content>', '<specific_content>'.$specific_content.'</specific_content>', SPECIFIC_CONTENT) : SPECIFIC_CONTENT;
    
    $api_key = $api_key ? $api_key : '';
    $ch = curl_init('https://api.openai.com/v1/chat/completions');

    if ($isFirstMessage && !empty($tematic_prompt)) {

        if(!empty($company_name)){
            $tematic_prompt = str_replace("[Nombre de la Compañía]", $company_name, $tematic_prompt);
            $specific_content = str_replace("[Nombre de la Compañía]", $company_name, $specific_content);
            $rules_prompt = str_replace("[Nombre de la Compañía]", $company_name, $rules_prompt);
        }

        array_unshift($conversation_history, [
            'role' => 'system',
            'content' => '"'.$tematic_prompt.$specific_content.$rules_prompt.'"'
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