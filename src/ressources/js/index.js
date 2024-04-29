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

$('#conducteurs-add').on('click', function () {
    let template = $('#conducteurs-add-template').html()
    Mustache.parse(template)
    let rendered = Mustache.render(template, {
        indice: $('#conducteurs-lignes tr').length
    })
    $('#conducteurs-lignes').prepend(rendered)
    $('.conducteurs-delete').on('click', function () {
        $(this).parent().parent().remove()
    })
})

$(document).ready(function () {
    $('#client-search').select2({
        minimumInputLength: 3, // Nombre minimal de caractères pour déclencher la recherche
        placeholder: 'Rechercher un client',
        ajax: {
            url: '/administration/reservation/search-clients', // Endpoint côté serveur
            dataType: 'json',
            delay: 250, // Délai avant d'effectuer la recherche après la saisie
            data: function (params) {
                return {
                    client_search: params.term // Terme de recherche saisi par l'utilisateur
                }
            },
            processResults: function (data) {
                return {
                    results: data // Résultats reçus depuis le serveur
                }
            },
            cache: true
        }
    })

    $('#client-search').on('select2:select', function (e) {
        updateClientInfo(e.params.data)
    })

    function updateClientInfo (client) {
        if (client.is_client) {
            client.client_id = client.id
            $('#create-user-field').addClass('d-none')
        } else {
            $('#create-user-field').removeClass('d-none')
        }

        for (var key in client) {
            if (client.hasOwnProperty(key)) {
                var input = document.querySelector('[name="' + key + '"]')
                if (input) {
                    if (input.type === 'date') {
                        // Pour les inputs de type "date", formatez la valeur en utilisant Carbon
                        input.value = formatDate(client[key])
                    } else {
                        input.value = client[key]
                    }
                }
            }
        }
    }

    function formatDate (dateString) {
        // Utilisez Carbon pour formater la date
        var formattedDate = new Date(dateString).toISOString().slice(0, 10)
        return formattedDate
    }
})
