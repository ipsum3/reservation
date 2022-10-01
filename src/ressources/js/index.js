import * as $ from 'jquery'

$('#tarification-load').click(function () {
    $.ajax({
        method: 'POST',
        url: $(this).data('ajax-url'),
        data: $('#reservation').serialize(),
        success: function (data) {
            $('#tarification-alert').hide()
            $('#tarification').html(data.tarification)
        },
        error: function (xhr, type, exception) {
            $('#tarification-alert').show().html(xhr.responseJSON.message)
        }
    })
})
