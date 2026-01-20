<style>
    .progressbar {
        margin-bottom: 10px;
        padding: 0;
        flex-wrap: wrap;
        justify-content: space-around;
        text-align: center;
        counter-reset: step;
    }
    .progressbar li {
        margin-bottom: 20px;
        padding-left: 10px;
        list-style-type: none;
        font-size: 12px;
        position: relative;
        color: #7d7d7d;
    }
    .progressbar li:before {
        content: counter(step);
        counter-increment: step;
        width: 30px;
        height: 30px;
        line-height: 27px;
        border: 2px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
    }
    .progressbar li.active {
        color: #26b2ed;
    }
    .progressbar li.active:before {
        border-color: #26b2ed;
    }
    .progressbar li a {
        color: #7d7d7d;
    }
    @media screen and (max-width: 640px) {
        .progressbar li {
            font-size: 10px
        }
        .progressbar li:before {
            width: 20px;
            height: 20px;
            line-height: 18px;
            border: 1px solid #7d7d7d;
            margin-bottom: 5px;
        }
    }
</style>

@php
$etapes = [
    [
        'route' => 'admin.inspection.vehicule',
        'nom' => 'Véhicule',
        'show' => $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID
    ],
    [
        'route' => 'admin.inspection.client',
        'nom' => 'Réservation',
        'show' => $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID
    ],
    [
        'route' => 'admin.inspection.checklist',
        'nom' => 'Checklist',
        'show' => true
    ],
    [
        'route' => 'admin.inspection.dommages',
        'nom' => 'Dommages',
        'show' => true
    ],
    [
        'route' => 'admin.inspection.recapitulatif',
        'nom' => 'Récapitulatif',
        'show' => true
    ],
    [
        'route' => 'admin.inspection.signature.locataire',
        'nom' => 'Signature client',
        'show' => true
    ],
    [
        'route' => 'admin.inspection.signature.agent',
        'nom' => 'Signature agent',
        'show' => true
    ],
];

$has_link = true;
@endphp

<ul class="progressbar d-flex">
    @foreach($etapes as $etape)
        @if($etape['show'])
            <li class="{{ request()->routeIs($etape['route']) ? 'active' : '' }}">
                @php
                if (request()->routeIs($etape['route'])) {
                    $has_link = false;
                }
                @endphp
                @if($has_link)
                    <a href="{{ route($etape['route'], [$reservation, $type]) }}">{{ $etape['nom'] }}</a>
                @else
                    {{ $etape['nom'] }}
                @endif
            </li>
        @endif
    @endforeach
</ul>
