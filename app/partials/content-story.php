<?php
/**
 * Single story post type content template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-story" id="story">
        <div class="entry-story__loader">
            <span class="entry-story__loader-bounce"></span>
        </div>

        <div class="entry-story__track" data-glide-el="track">
            <div class="entry-story__slides">
                <div class="entry-story__slide">
                    <div class="entry-story__slide-wrap entry-story__slide-wrap--head">
                        <div class="entry-story__slide-content">
                            <?php
                                the_info(
                                    '<div class="entry-story__slide-info info">', '</div>',
                                    ['author', 'date']
                                );

                                the_title(
                                    '<h1 class="entry-story__slide-title">',
                                    '</h1>'
                                );

                                the_share(
                                    '<div class="entry-story__slide-share share">', '</div>',
                                    __('Share story — first', 'knife-theme')
                                );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
