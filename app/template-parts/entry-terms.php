<?php
/**
 * Entry terms template
 *
 * @package knife-theme
 * @since 1.1
 */

$terms = get_the_tags(); ?>

<?php if(count($terms) > 0) : ?>

	<div class="entry__terms">
		<?php foreach($terms as $term) : ?>

		<a class="entry__terms-link" href="<?php echo get_tag_link($term->term_id); ?>"><?php echo $term->name; ?></a>

		<?php endforeach; ?>
	</div>

<?php endif; ?>
