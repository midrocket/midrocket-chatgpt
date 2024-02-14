let conversationHistory = [];
let isFirstMessage = true;

jQuery(document).ready(function($) {
    $('#chatbot-send-btn').click(function() {
        var message = $('#chatbot-input').val();
        conversationHistory.push({role: "user", content: message});

        $('#chatbot-messages').append('<div class="user-message">' + message + '</div>');
        $('#chatbot-input').val('');
        $('#chatbot-send-btn').prop('disabled', true);
        $('#chatbot-loading').show();

        $.ajax({
            type: "POST",
            url: chatbotAjax.ajaxurl,
            data: {
                action: 'handle_chatbot_conversation',
                message: JSON.stringify(conversationHistory),
                isFirstMessage: isFirstMessage
            },
            success: function(response) {
                $('#chatbot-messages').append('<div class="chatbot-response">' + response + '</div>');
                conversationHistory.push({role: "assistant", content: response});
                isFirstMessage = false;
            },
            complete: function() {
                $('#chatbot-send-btn').prop('disabled', false);
                $('#chatbot-loading').hide();
            }
        });
    });
});


jQuery(document).ready(function($) {
    $('#chatbot-input').keypress(function(e) {
        if(e.which == 13) { // Enter key pressed
            $('#chatbot-send-btn').click(); // Trigger send button click event
            e.preventDefault(); // Prevent the default action to stop form submission
        }
    });
});