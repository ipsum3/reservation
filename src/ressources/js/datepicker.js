import * as $ from 'jquery'
import 'daterangepicker'
import moment from 'moment'

$('.datepicker-range').daterangepicker({
    autoUpdateInput: false,
    ranges: {
        'Aujourd\'hui': [moment(), moment()],
        'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '7 derniers jours': [moment().subtract(6, 'days'), moment()],
        '30 derniers jours': [moment().subtract(29, 'days'), moment()],
        'Mois en cours': [moment().startOf('month'), moment().endOf('month')],
        'Mois précédent': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
