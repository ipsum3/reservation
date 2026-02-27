@extends('IpsumAdmin::layouts.app')
@section('title', 'Envoi d\'un document')

@section('content')

    <h1 class="main-title">Envoie de document par email</h1>

    {{ Aire::open()->id('reservation')->route( 'admin.reservation.documentSend', $reservation)->bind($reservation)->formRequest(\Ipsum\Reservation\app\Http\Requests\SendDocumentEmail::class) }}
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">{{ request('objet') }}</h2>
                <div class="btn-toolbar">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$reservation]) }}">Retour à la réservation</a>&nbsp;
                    <button class="btn btn-primary" type="submit"><i class="fas fa-envelope"></i> Envoyer le document</button>
                </div>
            </div>
            <div class="box-body">
                <input type="hidden" value="{{ $document }}" name="document"/>
                <input type="hidden" value="{{ request('id') }}" name="id"/>
                <div class="form-row">
                    {{ Aire::input('email', 'Email*')->required()->groupAddClass('col-md-6') }}
                    {{ Aire::input('objet', 'Objet*')->required()->defaultValue(request('objet'))->groupAddClass('col-md-6') }}
                    @if ($document != 'confirmation')
                        {{ Aire::textArea('message', 'Message*')->required()->defaultValue('Veuillez trouver ci-joint votre document.')->groupAddClass('col-md-6') }}
                    @endif
                </div>
            </div>
        </div>
    {{ Aire::close() }}

@endsection