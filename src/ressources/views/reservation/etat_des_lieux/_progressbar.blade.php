<style>
    .step { display: none; }
    .step.active { display: block; }
    .progressbar {
        counter-reset: step;
        width: 100%;
    }
    .progressbar li {
        list-style-type: none;
        width: 12.5%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        color: #7d7d7d;
        min-width: 120px;
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
        background-color: white;
    }
    .progressbar li.active {
        color: #26b2ed;
    }
    .progressbar li.active:before {
        border-color: #26b2ed;
    }
</style>

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
</script>