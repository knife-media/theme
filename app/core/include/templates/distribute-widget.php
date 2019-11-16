<div id="knife-distribute-widget">
    <?php
        $items = [];

        // Get all cron tasks array
        $cron = (array) _get_cron_array();

        // Define distribute config
        $conf = KNIFE_DISTRIBUTE ?? [];

        foreach($cron as $timestamp => $tasks) {
            foreach($tasks as $hook => $task) {
                if($hook !== 'knife_schedule_distribution') {
                    continue;
                }

                foreach($task as $key => $schedule) {
                    if(empty($schedule['args']) || count($schedule['args']) < 2) {
                        continue;
                    }

                    $items[] = [
                        'timestamp' => $timestamp,
                        'uniqid' => $schedule['args'][0],
                        'post_id' => $schedule['args'][1]
                    ];
                }
            }
        }

        ksort($items);
    ?>

    <?php if(empty($items)) : ?>

        <div class="box box--blank">
            <span><?php _e('Ничего не запланировано', 'knife-theme'); ?></span>
        </div>

    <?php else : ?>

        <div class="box box--items">
            <?php foreach($items as $timestamp => $item) : ?>
                <div class="item">
                    <?php
                        extract($item);

                        // Get all tasks by post id
                        $tasks = get_post_meta($post_id, self::$meta_items, true);

                        if(empty($tasks[$uniqid])) {
                            continue;
                        }

                        // Task by uniqid
                        $task = $tasks[$uniqid];

                        if(empty($task['targets'])) {
                            continue;
                        }
                    ?>

                    <div class="item__header">
                        <span class="dashicons dashicons-clock"></span>

                        <?php
                            $timestamp = date('Y-m-d H:i:s', $timestamp);

                            printf('<strong>%s</strong>',
                                get_date_from_gmt($timestamp, 'd.m.Y G:i')
                            );

                            printf('<a href="%s" target="_blank">%s</a>',
                                esc_url(get_edit_post_link($post_id)),
                                get_permalink($post_id)
                            );
                        ?>
                    </div>

                    <?php
                        if(!empty($task['excerpt'])) {
                            $excerpt = preg_replace('~\*|\_(.+?)\*|\_~is', "$1", wpautop($task['excerpt']));

                            printf('<div class="item__content">%s</div>',
                                wp_specialchars_decode($excerpt)
                            );
                        }

                        if(!empty($task['attachment'])) {
                            printf('<img class="item__poster" src="%s" alt="">',
                                wp_get_attachment_image_url($task['attachment'])
                            );
                        }
                    ?>

                    <div class="item__targets">
                        <?php
                            foreach($task['targets'] as $target) {
                                if(empty($conf[$target]['label'])) {
                                    continue;
                                }

                                printf('<span class="item__targets-network">%s</span>',
                                    $conf[$target]['label']
                                );
                            }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>
