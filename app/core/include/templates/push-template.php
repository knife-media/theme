<?php
/**
 * Display push template
 *
 * @package knife-theme
 * @since 1.3
 */

?>

<div class="push push--hide">
	<p class="push__promo"><?php _e('Получать последние обновления&nbsp;сайта', 'knife-theme'); ?></p>

	<button class="push__button" data-action="Push Notifications — Accept">
		<span class="icon icon--notify"></span>
		<?php _e('Подписаться', 'knife-theme'); ?>
	</button>

	<button class="push__close" data-action="Push Notifications — Decline"></button>
</div>
