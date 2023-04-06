import * as $ from 'jquery'
import Mustache from 'mustache'
import 'ipsum3-admin-assets/src/js/index'
import './datepicker'

$('#reservation-categorie').on('change', function () {
    $.ajax({
        method: 'GET',
        url: $(this).data('ajax-url'),
        data: $('#reservation').serialize(),
        success: function (data) {
            $('#vehicule-alert').hide()
            $('#vehicule-select').html(data.select)
        },
        error: function (xhr, type, exception) {
            $('#vehicule-alert').show().html(xhr.responseJSON.message)
        }
    })
})

$('#tarification-load, #tarification-undo').click(function () {
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

    if ($(this).attr('id') === 'tarification-undo') {
        $('#tarification-undo').hide()
    } else {
        $('#tarification-undo').show()
    }
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
