<?php
/**
 * Entry tags template
 *
 * @package knife-theme
 * @since 1.1
 */

$tags = get_the_tags(); ?>

<?php if($tags) : ?>

	<div class="entry__tags">

<?php foreach($tags as $tag) : ?>
		<a class="entry__tags-link" href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo $tag->name; ?></a>
<?php endforeach; ?>

	</div>

<?php endif; ?>
