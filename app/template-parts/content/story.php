
    <style>
        html, body {
        }

        .swiper-text {
            display: block;
            margin-left: 100px;
            width: 50%;
            color: #fff;
            font-size: 28px;
            font-weight: 500;
        }

        .swiper-pagination-bullet {
            background: #fff;
        }

        @media screen and (max-width: 1023px) {
            .swiper-text {
                font-size: 14px;
            }
        }
    </style>

    <div class="slider swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img class="slider__background" src="https://picsum.photos/1440/800/?image=1">

                <div class="slider__content block">

                    <div class="slider__text">
                        <p>Для начала — насчет невроза. Ни разумная, ни некая неразумная рефлексия не могут перейти в невроз, потому что невроз в этом смысле всегда первичен. </p>
                        <p>Субъекты с соответствующей неврозацией склонны обращать рефлексию в орудие самоуничижения или называть свои самоуничижающие мысли рефлексией.</p>
                    </div>
                </div>
            </div>
            <div class="swiper-slide" style="background-image: url(https://picsum.photos/1440/800/?image=22)">
                <div class="slider__content block">
                    <p>То, что принято называть самоедством или самокопанием, походит на рефлексию или самоанализ тем, что в обоих случаях мысль субъекта направлена на него самого. </p>
                    <p>Ни то, ни другое никогда не станет объективным способом постижения себя, потому что мысли человека всегда связываются им с другими мыслями и окрашиваются его отношениями. </p>
                    <p>И уж тем более, когда это касается его самого. Однако отсутствие объективности еще не означает, что этот процесс неэффективен.</p>
                </div>
            </div>
            <div class="swiper-slide" style="background-image: url(https://picsum.photos/1440/800/?image=30)">
                <div class="slider__content block">
                        <p>Отличие самоедства от рефлексии заключается в переживательной окраске этого процесса, поскольку в первом случае постижение себя отходит на второй план и является лишь средством для усиления вины, тревоги и ненависти к себе.</p>
                </div>
            </div>
        </div>

        <div class="swiper-pagination"></div>

        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.2.0/js/swiper.min.js"></script>

    <script>
        var swiper = new Swiper('.swiper-container', {
            effect: 'cube',
            grabCursor: true,
            cubeEffect: {
                shadow: true,
                slideShadows: true,
                shadowOffset: 20,
                shadowScale: 0.94,
            },
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
