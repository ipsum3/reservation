import * as $ from 'jquery'
import Mustache from 'mustache'

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

$('#paiement-add').on('click', function () {
    let template = $('#paiement-add-template').html()
    Mustache.parse(template)
    let rendered = Mustache.render(template, {
        indice: $('#paiement-lignes tr').length
    })
    $('#paiement-lignes').prepend(rendered)
    $('.paiement-delete').on('click', function () {
        $(this).parent().parent().remove()
    })
})
