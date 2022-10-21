@props(['total', 'montant_paye'])

@if ((float) $total >= (float) $montant_paye)
    <span class="badge {{ (float) $total == (float) $montant_paye ? 'badge-light' : 'badge-danger' }}">
        @prix((float) $total - (float) $montant_paye) &nbsp;€ reste à payer
    </span>
@else
    <span class="badge badge-warning">
        @prix((float) $montant_paye - (float) $total) &nbsp;€ de trop perçu
     </span>
@endif