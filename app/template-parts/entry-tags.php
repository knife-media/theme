 <div class="entry__tags">
			<?php $posttags = get_the_tags();

			if ($posttags) { ?>
            <div class="post__tags post__block mb-4">
              <ul class="grid grid--words">
			  <?php foreach($posttags as $tag) {

				  echo '<li class="grid__item"><a class="link link--large" href="'. get_tag_link($tag->term_id).'">'.$tag->name.'</a></li>';

			  }?>
              </ul>
            </div>
			<?php }
?>
</div>
