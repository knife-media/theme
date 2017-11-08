       <div class="grid__item height-fix col-12 col-lg-3 mt-lg-0">
        <div class="flex-auto height-fix justify-content-between">
			<div class="grid grid--small-gaps d-flex flex-column">
					<?php
						global $curr_page;

						$sidebar_num = abs((int)$_GET["gtsbr"]);


						$cat_news = get_category_by_slug('news');
						$news_id = $cat_news->term_id;
						$news_link =  get_category_link( $news_id );
						$args = array( 'cat' => $news_id,'offset' => $sidebar_num*8, 'posts_per_page' => 8);



						$lastnews = get_posts($args);

						if($lastnews){
							foreach($lastnews as $post) {
								setup_postdata($post);
								get_template_part('loop/loop', 'last-news-sidebar');

							}
						}
						wp_reset_postdata();
					?>
				<div class="grid__item">
					<div class="show-more show-more--reversed-colors">
					  <?php if($ret_){?><a href="<?php echo $news_link;?>">
						<svg class="show-more__icon" height="27px" width="22.56px" x="0px" y="0px" viewBox="-206.5 -41.3 358 428.3">
						  <path class="show-more__path" d="M-117.3,296.9l-89.2-89.3v-56.7c0-31.2,0.3-56.7,0.8-56.7s30.5,29.7,66.7,66c37,37,66.8,66,67.7,66
							c1.7,0,1.8-7.2,2-133.7L-69-41.3h42h42l0.3,133.2c0.2,126.1,0.3,133.2,2,133.2c1,0,30.5-28.8,67.2-65.5c36-36,65.8-65.5,66.2-65.5
							c0.4,0,0.8,25.5,0.8,56.7v56.7l-89.2,89.3c-49.1,49.1-89.5,89.3-89.8,89.3S-68.2,346-117.3,296.9z"></path>
						</svg>

					  </a>
					  <?php }?>
					  <a href="<?php echo $news_link;?>" class="show-more__link text-uppercase">Больше новостей</a>
					</div>
				</div>

		   </div>
		   <section class="grid grid--small-gaps best-articles flex-column mt-3">
				<div class="grid grid--small-gaps grid--lg-modules mt-2">
					<?php
						$args = array( 'category__not_in' => array($news_id), 'offset' => $sidebar_num*3,  'posts_per_page' => 3);


						$postss = get_posts($args);


						if($postss){
							foreach($postss as $post) {
								setup_postdata($post);
								get_template_part('loop/loop', 'last-posts-sidebar');

							}
						}
						wp_reset_postdata();


					?>
				</div>
		   </section>
	   </div>
	  </div>
