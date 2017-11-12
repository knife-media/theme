<?php
/**
 * Entry share template
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<div class="entry__share">
	<p class="entry__share-title">Поделиться в соцсетях:</p>

	<div class="entry__share-list">
		<a class="entry__share-item entry__share-item--fb" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php the_permalink(); ?>&p[title]=<?php the_title(); ?>" target="_blank">
			<span class="icon icon--fb"></span>
			<span class="entry__share-action">Пошерить</span>
		</a>

		<a class="entry__share-item entry__share-item--vk" href="http://vk.com/share.php?url=<?php the_permalink(); ?>" target="_blank">
			<span class="icon icon--vk"></span>
			<span class="entry__share-action">Поделиться</span>
		</a>

		<a class="entry__share-item entry__share-item--telegram" href="https://t.me/share/url?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank">
			<span class="icon icon--telegram">
		</a>

		<a class="entry__share-item entry__share-item--twitter" href="https://twitter.com/intent/tweet?text=<?php the_title();?>&url=<?php the_permalink(); ?>" target="_blank">
			<span class="icon icon--twitter">
		</a>
	</div>
</div>
