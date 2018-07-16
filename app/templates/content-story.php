
<section class="glide">
    <article class="glide__track" data-glide-el="track">

        <div class="glide__slides">
            <header class="glide__slide">
                <?php
                    the_title(
                        '<h1 class="glide__slide-title">',
                        '</h1>'
                    );

                    the_lead(
                        '<div class="glide__slide-excerpt custom">',
                        '</div>'
                    );
                ?>
            </header>

            <?php
                the_content();
            ?>
        </div>

        <div class="glide__arrows" data-glide-el="controls">
            <button class="glide__arrow glide__arrow--left" data-glide-dir="<">prev</button>
            <button class="glide__arrow glide__arrow--right" data-glide-dir=">">next</button>
        </div>
    </article>

</section>
