<?php
/**
 * Single story post type content template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="glide__loader">
    <span class="glide__loader-bounce"></span>
</div>

<div class="glide__track" data-glide-el="track">
    <article class="glide__slides">
        <div class="glide__slide">
            <div class="glide__slide-wrap glide__slide-wrap--head">
                <div class="glide__slide-content">
                    <?php
                        the_info(
                            '<div class="glide__slide-meta meta">', '</div>',
                            ['author', 'date']
                        );

                        the_title(
                            '<h1 class="glide__slide-title">',
                            '</h1>'
                        );

                        the_share(
                            '<div class="glide__slide-share share">', '</div>',
                            __('Share story — first', 'knife-theme')
                        );
                    ?>
                </div>
            </div>
        </div>
    </article>
</div>
