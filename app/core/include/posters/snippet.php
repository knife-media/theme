<?php
/**
 * Snippet poster template
 *
 * Use for social image snippet
 * Logo and post title
 */

$poster = new PHPImage();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->resize(1024, 512, true, true);


// Change brightness and contrast
$filter = $poster->getResource();
imagefilter($filter, IMG_FILTER_CONTRAST, 35);
imagefilter($filter, IMG_FILTER_BRIGHTNESS, -85);
$poster->setResource($filter);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 70, 40);


// Draw site name
/*
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');
$poster->text('knife.media', [
    'x' => 48, 'y' => 40, 'fontSize' => 16
]);
 */


// Draw title
if(!empty($textbox['title'])) {
    $poster->textBox($textbox['title'], [
        'x' => 48, 'y' => 160, 'width' => 950, 'height' => 200, 'fontSize' => 24
    ]);
}

$poster->snapshot($basedir . $filename);
