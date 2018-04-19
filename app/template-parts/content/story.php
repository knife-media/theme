    <div class="slider swiper-container">
        <div class="parallax-bg" style="background-image:url(<?php echo get_post_meta(get_the_ID(), 'knife-story-background', true); ?>);" data-swiper-parallax="-23%"></div>
        <div class="swiper-wrapper">

            <?php
                $stories = get_post_meta(get_the_ID(), 'knife-story-stories');
                $shadow = get_post_meta(get_the_ID(), 'knife-story-shadow', true);


                $i = 0;
                foreach($stories as $slide) : $i++;
            ?>
            <div class="swiper-slide"  data-hash="slide<?php echo $i; ?>">

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



    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.2.0/js/swiper.min.js"></script>

    <script>
        var swiper = new Swiper('.swiper-container', {
            parallax: true,
                hashNavigation: {
                  watchState: true,
                },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
        });
    </script>
