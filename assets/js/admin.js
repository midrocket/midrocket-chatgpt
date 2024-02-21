var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function () {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}

// jQuery(document).ready(function($) {
//   var wrapper = $('#knowledge-repeater');
//   var add_button = $('#add-knowledge-pair');

//   $(add_button).click(function(e) {
//     console.log('works');
//       e.preventDefault();
//       var new_index = wrapper.find('.knowledge-pair').length;
//       var new_field = '<div class="knowledge-pair">' +
//                       '<input type="text" name="midrocket_chatbot_gpt_options[knowledge][' + new_index + '][question]" value="" placeholder="Question" />' +
//                       '<textarea name="midrocket_chatbot_gpt_options[knowledge][' + new_index + '][answer]" placeholder="Answer"></textarea>' +
//                       '<button type="button" class="remove-knowledge-pair">Remove</button>' +
//                       '</div>';
//       wrapper.append(new_field);
//   });

//   wrapper.on('click', '.remove-knowledge-pair', function(e) {
//       e.preventDefault();
//       $(this).parent('div').remove();
//   });
// });

jQuery(document).ready(function ($) {
  $('#knowledge-repeater').on('click', '.edit-knowledge-pair, .knowledge-question-title', function () {
    $(this).closest('.knowledge-pair').find('.knowledge-edit-form').toggle();
  });

  $('#add-knowledge-pair').click(function () {
    var new_index = $('#knowledge-repeater .knowledge-pair').length;
    $(this).before(`
          <div class="knowledge-pair" data-index="${new_index}">
              <div class="knowledge-summary">
                  <span class="knowledge-question-title">New Question</span>
                  <div class="knowledge-actions">
                    <button type="button" class="button button-secondary button-remove remove-knowledge-pair">Remove</button>
                    <button type="button" class="button button-secondary edit-knowledge-pair">Edit</button>
                  </div>
              </div>
              <div class="knowledge-edit-form" style="display: none;">
                  <input type="text" class="question-input" name="midrocket_chatbot_gpt_options[knowledge][${new_index}][question]" value="" placeholder="Question" />
                  <textarea name="midrocket_chatbot_gpt_options[knowledge][${new_index}][answer]" placeholder="Answer" rows="4"></textarea>
                  <button type="button" class="button button-secondary remove-knowledge-pair">Remove</button>
              </div>
          </div>
      `);
  });

  $('#knowledge-repeater').on('click', '.remove-knowledge-pair', function () {
    $(this).closest('.knowledge-pair').remove();
  });

  $('#knowledge-repeater').on('input', '.question-input', function () {
    var updatedQuestion = $(this).val();
    $(this).closest('.knowledge-pair').find('.knowledge-question-title').text(updatedQuestion);
  });

  $('#midrocket_chatbot_gpt_connect_btn').click(function () {
    var $btn = $(this);
    var apiKey = $('#midrocket_chatbot_gpt_api_key').val();
    $btn.find('.spinner').css('display', 'inline-block');

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'verify_openai_api_key',
        api_key: apiKey,
      },
      success: function (response) {
        if (response.success) {
          $('#midrocket_chatbot_gpt_api_key_status').text(response.data.message).removeClass('api-key-invalid').addClass('api-key-valid');
        } else {
          $('#midrocket_chatbot_gpt_api_key_status').text(response.data.message).removeClass('api-key-valid').addClass('api-key-invalid');
        }
      },
      complete: function () {
        $btn.find('.spinner').css('display', 'none');
      }
    });
  });

});