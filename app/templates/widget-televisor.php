<?php
/**
 * Televisor widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.17
 */
?>

<div class="widget-televisor__wrapper">
    <div class="widget-televisor__content">
        <?php
            $this->show_single( $instance );
            $this->show_units( $instance );
        ?>
    </div>

    <div class="widget-televisor__sidebar">
        <?php
            $this->show_recent( $instance );
        ?>
    </div>
</div>
