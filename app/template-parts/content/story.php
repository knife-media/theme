<div class="slider swiper-container">
    <div class="swiper-wrapper">

        <div class="slider__slide swiper-slide"  data-hash="slide-<?php echo $i; ?>">
            <div class="slider__content block">

                <div class="slider__text">
                    <?php echo wpautop($slide['text']); ?>
                </div>
            </div>
        </div>

        <?php
            $stories = get_post_meta(get_the_ID(), 'knife-story-stories');

            foreach($stories as $i => $slide) :
        ?>
        <div class="slider__slide swiper-slide"  data-hash="slide-<?php echo $i; ?>">
            <div class="slider__content block">

                <div class="slider__text">
                    <?php echo wpautop($slide['text']); ?>
                </div>
            </div>
        </div>

        <?php endforeach; ?>

    </div>

    <div class="swiper-pagination"></div>

    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>
