let conversationHistory = [];
let isFirstMessage = true;

jQuery(document).ready(function($) {

    $('#chatbot-input').on('input', function() {
        if ($(this).val().trim().length > 0) {
            $('#chatbot-send-btn').prop('disabled', false);
        } else {
            $('#chatbot-send-btn').prop('disabled', true);
        }
    });

    $('#chatbot-input').keypress(function(e) {
        if(e.which == 13) { // Enter key pressed
            if (!$('#chatbot-send-btn').prop('disabled')) {
                $('#chatbot-send-btn').click();
            }
            e.preventDefault();
        }
    });

    $('#chatbot-send-btn').click(function() {
        var message = $('#chatbot-input').val();
        conversationHistory.push({role: "user", content: message});
        
        $('#chatbot-messages').prepend('<div class="user-message">' + message + '</div>');
        $('#chatbot-messages').prepend('<div class="typing-message"><div class="typing"><span></span><span></span><span></span></div></div>');
        $('#chatbot-input').val(' ').prop('disabled', true); // Hide placeholder
        $('#chatbot-intro').hide();
        $('#chatbot-send-btn').prop('disabled', true);
        //$('#chatbot-loading').show();
        $('#chatbot-loading').css('display', 'flex');

        $.ajax({
            type: "POST",
            url: chatbotAjax.ajaxurl,
            data: {
                action: 'handle_chatbot_conversation',
                message: JSON.stringify(conversationHistory),
                isFirstMessage: isFirstMessage
            },
            success: function(response) {
                $('.typing-message').remove();
                $('#chatbot-messages').prepend('<div class="chatbot-response">' + response + '</div>');
                conversationHistory.push({role: "assistant", content: response});
                isFirstMessage = false;
            },
            complete: function() {
                $('#chatbot-send-btn').prop('disabled', false);
                $('#chatbot-input').val('').prop('disabled', false).focus();
                $('#chatbot-loading').hide();
            }
        });
    });

});