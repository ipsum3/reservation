@extends('IpsumAdmin::layouts.app')
@section('title', 'Envoi document')

@section('content')

    <h1 class="main-title">Envoyer {{ $document == 'confirmation' ? 'la' : 'le'}} {{ $document }} par email</h1>

    {{ Aire::open()->id('reservation')->route( 'admin.reservation.documentSend', $reservation)->bind($reservation)->formRequest(\Ipsum\Reservation\app\Http\Requests\SendDocumentEmail::class) }}
        <div class="box">
            <div class="box-header">
                <input type="hidden" value="{{ $document }}" name="document"/>
                <h2 class="box-title">Saisissez une adresse email</h2>
                <div class="btn-toolbar">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$reservation]) }}">Retour à la réservation</a>&nbsp;
                    <button class="btn btn-primary" type="submit"><i class="fas fa-envelope"></i> Envoyer {{ $document == 'confirmation' ? 'la' : 'le'}} {{ $document }}</button>
                </div>
            </div>
            <div class="box-body">
                <input type="hidden" value="{{ $reservation->id }}" name="reservation_id"/>
                {{ Aire::input('email', 'Email') }}
            </div>
        </div>
    {{ Aire::close() }}

@endsection