<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="<?php echo esc_attr($options['description']); ?>">

    <meta property="og:site_name" content="Нож" />
    <meta property="og:locale" content="ru_RU" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo esc_attr($options['caption']); ?>" />
    <meta property="og:description" content="<?php echo esc_attr($options['description']); ?>" />
    <meta property="og:image" content="<?php echo esc_attr($options['poster']); ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:site" content="@knife_media" />
    <meta property="twitter:creator" content="@knife_media" />
    <meta property="twitter:title" content="<?php echo esc_attr($options['caption']); ?>" />
    <meta property="twitter:description" content="<?php echo esc_attr($options['description']); ?>" />
    <meta property="twitter:image" content="<?php echo esc_attr($options['poster']); ?>" />
    <meta property="vk:image" content="<?php echo esc_attr($options['poster']); ?>" />

    <title><?php echo esc_attr($options['caption']); ?></title>
</head>
<body>
    <script>
        window.location.href = '<?php the_permalink($post_id) ?>';
    </script>
</body>
</html>
