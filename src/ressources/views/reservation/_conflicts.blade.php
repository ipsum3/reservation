@if(isset($conflicts) and $conflicts->count())
    <div class="alert alert-danger">
        <p><strong><i class="fas fa-exclamation-triangle"></i> Conflits potentiels :</strong></p>
        <ul>
            @foreach($conflicts as $conflict)
                @if(get_class($conflict) != \Ipsum\Reservation\app\Models\Reservation\Reservation::class)
                    <li>La réservation est en conflit avec l'intervention {{ "#".$conflict->id }} "{{ $conflict->type->nom }}" du {{ $conflict->debut_at->format('d/m/Y H:i') }} au {{ $conflict->fin_at->format('d/m/Y H:i') }}</li>
                @else
                    <li>La réservation est en conflit avec la réservation {{ "#".$conflict->reference }} du {{ $conflict->debut_at->format('d/m/Y H:i') }} au {{ $conflict->fin_at->format('d/m/Y H:i') }}</li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
