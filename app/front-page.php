<?php
/**
 * Template for showing site front-page
 *
 * Difference between front-page and home.php in a link below
 *
 * @link https://wordpress.stackexchange.com/a/110987
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

<?php if(is_active_sidebar('knife-under-header')) : ?>
    <div class="content">
        <?php dynamic_sidebar('knife-under-header'); ?>
    </div>
<?php endif; ?>


<?php if(is_active_sidebar('knife-feature-stripe')) : ?>
    <div class="content block">
        <div class="split split--content">
            <?php dynamic_sidebar('knife-feature-stripe'); ?>
        </div>

    <?php if(is_active_sidebar('knife-feature-sidebar')) : ?>
        <aside class="split split--sidebar">
            <?php dynamic_sidebar('knife-feature-sidebar'); ?>
        </aside>
    <?php endif; ?>
    </div>
<?php endif; ?>


<?php if(is_active_sidebar('knife-frontal')) : ?>
    <div class="content block up">
        <?php dynamic_sidebar('knife-frontal'); ?>
    </div>

     <div class="nav block">
        <a class="button" href="<?php echo home_url('/recent/page/4/'); ?>"><?php _e('Больше статей', 'knife-media'); ?></a>
    </div>
<?php endif; ?>

</main>

<?php

get_footer();
