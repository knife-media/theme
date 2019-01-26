<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo esc_attr($description); ?>">

    <meta property="og:title" content="<?php echo esc_attr($caption); ?>">
    <meta property="og:site_name" content="НОЖ">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:image" content="<?php echo esc_attr($poster); ?>">
    <meta property="og:image:width" content="800">
    <meta property="og:image:height" content="420">

    <meta name="twitter:card" content="photo">
    <meta name="twitter:site" content="@knife_media">
    <meta name="twitter:creator" content="@knife_media">
    <meta name="twitter:title" content="<?php echo esc_attr($caption); ?>">
    <meta name="twitter:image" content="<?php echo esc_attr($poster); ?>">
    <meta name="twitter:url" content="">

    <title><?php echo esc_attr($caption); ?></title>
</head>
<body>
    <script type="text/javascript">
        window.location.href = '<?php the_permalink($post_id) ?>';
    </script>
</body>
</html>
