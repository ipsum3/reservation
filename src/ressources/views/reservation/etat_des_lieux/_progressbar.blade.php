<style>
    .progressbar {
        margin-bottom: 30px;
        padding: 0;
        flex-wrap: wrap;
        justify-content: space-around;
        text-align: center;
        counter-reset: step;
    }
    .progressbar li {
        margin-top: 10px;
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
</style>

{{--
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.querySelector('.progressbar');
        const activeItem = progressBar?.querySelector('.active');

        if (progressBar && activeItem) {
            // On calcule la position pour centrer l'élément actif
            const scrollLeft = activeItem.offsetLeft
                - (progressBar.clientWidth / 2)
                + (activeItem.clientWidth / 2);

            // On fait défiler en douceur vers cette position
            progressBar.scrollTo({
                left: scrollLeft,
                behavior: 'smooth'
            });
        }
    });
</script>--}}

<ul class="progressbar d-flex">
    @if($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
        <li class="active">Véhicule</li>
        <li>Réservation</li>
    @endif
    <li>Checklist</li>
    <li>Dommages</li>
    <li>Récapitulatif</li>
    <li>Signature client</li>
    <li>Signature agent</li>
</ul>
