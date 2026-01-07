import * as $ from 'jquery'
import 'ipsum3-admin-assets/src/js/index'
import './datepicker'
import 'daterangepicker'
import moment from 'moment'

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

$('.ajust-button').click(function () {
    let valeur = parseFloat($(this).parents('.box').find('.ajust-valeur').val())
    let type = $(this).parents('.box').find('.ajust-type').val()
    if (!isNaN(valeur)) {
        $(this).parents('.box').find('.montant').each(function () {
            let montant = parseFloat($(this).val())
            let val = 0

            if (type === 'pourcentage') {
                val = ((montant * valeur / 100) + montant).toFixed(2)
            } else {
                val = montant + valeur
            }
            $(this).val(val)
        })
    }
    return false
})

$('.datepicker-range-next').daterangepicker({
    autoUpdateInput: false,
    ranges: {
        'Aujourd\'hui': [moment(), moment()],
        'Demain': [moment().add(1, 'days'), moment().add(1, 'days')],
        '7 prochains jours': [moment(), moment().add(6, 'days')]
    },
    locale: {
        'format': 'DD/MM/YYYY',
        'applyLabel': 'Valider',
        'cancelLabel': 'Annuler',
        'fromLabel': 'Du',
        'toLabel': 'Au',
        'customRangeLabel': 'Personnalisée',
        'weekLabel': 'W',
        'daysOfWeek': [
            'di',
            'Lu',
            'Ma',
            'Me',
            'Je',
            'Ve',
            'Sa'
        ],
        'monthNames': [
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Aout',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre'
        ],
        'firstDay': 1
    }
}).on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'))
}).on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('')
})

$('#switch-categorie').collapse()

$('#switch-categorie, #switch-lieu').on('click', function (event) {
    if (!$(this).prop('checked')) {
        if (!window.confirm('Souhaitez-vous vraiment désactiver ?')) {
            return false
        }
        $($(this).data('collapse')).collapse('hide')
    } else {
        $($(this).data('collapse')).collapse('show')
    }
})

$('#collapse-categories, #collapse-lieux').on('hidden.bs.collapse', function (event) {
    console.log('rrr')
    $(this).find('input[type="checkbox"]').prop('checked', false)
    $(this).find('input[type="number"]').val('')
}).on('show.bs.collapse', function () {
    $(this).find('input[type="checkbox"]').prop('checked', true)
})
