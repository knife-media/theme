<?php
/**
 * Single template sidebar
 *
 * Show last news and related posts
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.17
 */
?>

<aside class="sidebar">
    <?php
    if ( is_active_sidebar( 'knife-sidebar' ) ) :
        dynamic_sidebar( 'knife-sidebar' );
    endif;
    ?>
</aside>
