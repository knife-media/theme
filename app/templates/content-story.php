<?php
/**
 * Story content template
 *
 * @package knife-theme
 * @since 1.3
 */
?>

<section class="glide">

    <div class="glide__track" data-glide-el="track">

        <article class="glide__slides">
            <div class="glide__slide">
                <div class="glide__slide-wrap">
                    <?php
                        the_info(
                            '<div class="glide__slide-meta meta">', '</div>',
                            ['author', 'date']
                        );

                        the_title(
                            '<h1 class="glide__slide-title">',
                            '</h1>'
                        );
                    ?>
                </div>
            </div>

            <?php
                the_story(
                    '<div class="glide__slide">',
                    '</div>'
                );
            ?>
        </article>

    </div>

</section>
