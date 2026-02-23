@component('mail::message')
Bonjour,

{{ $inspection->dommages->count() }} nouveau{{ $inspection->dommages->count() > 1 ? 'x' : '' }} dommage{{ $inspection->dommages->count() > 1 ? 's' : '' }} déclaré{{ $inspection->dommages->count() > 1 ? 's' : '' }}.

[Voir l'état des lieux]({{ route('admin.inspection.show', [$inspection]) }})

Cordialement.
@endcomponent
