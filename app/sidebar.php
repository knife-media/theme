<?php
/**
 * Single template sidebar
 *
 * Show last news and related posts
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<?php if(is_active_sidebar('knife-sidebar')) : ?>
    <aside class="sidebar">
        <?php dynamic_sidebar('knife-sidebar'); ?>
    </aside>
<?php endif; ?>
