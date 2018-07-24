<?php
/**
 * Story content template
 *
 * @package knife-theme
 * @since 1.3
 */
?>

<section class="content glide">

    <?php while (have_posts()) : the_post(); ?>
        <div class="glide__track" data-glide-el="track">

            <article class="glide__slides">
                <div class="glide__slide">
                    <div class="glide__slide-wrap glide__slide-wrap--first">
                        <?php
                            the_info(
                                '<div class="glide__slide-meta meta">', '</div>',
                                ['author', 'date']
                            );

                            the_title(
                                '<h1 class="glide__slide-title">',
                                '</h1>'
                            );

                            the_lead(
                                '<div class="glide__slide-excerpt">',
                                '</div>'
                            );

                            the_share(
                                '<div class="glide__slide-share share">',
                                '</div>',
                                __('Share story — top', 'knife-theme')
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
    <?php endwhile; ?>

</section>
