 <?php
	//$current_author = get_userdata($post->post_author);
	$cats=get_the_category();
	$cat_name=$cats[0]->cat_name;
	$cat_link =  get_category_link( $cats[0]->term_id );



	?>
<?php

			$category_id = $cats[0]->cat_ID;

			$args=array(
				'cat' => $category_id,
				'entry__not_in' => array($post->ID),
				'posts_per_page'=>6
			);

			$related = get_posts($args);

			if($related){

			?>
			<section class="entry__more-posts more-posts">
				<div class="entry__block more-posts__item">
					<h2 class="more-posts__title h3 font-weight-normal">Читайте также:</h2>
				</div>
				<?php foreach($related as $post) {
				setup_postdata($post);
				?>
				<div class="entry__block more-posts__item">
					<h3 class="h3 more-posts__heading">
						<a href="<?php echo get_permalink();?>" class="brand-link"><?php echo do_shortcode(get_the_title());?></a>
					</h3>
				</div>

				<?php } ?>
   </section>
			<?php }?>
			<?php wp_reset_query();?>
