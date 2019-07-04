<?php
/**
 * Snippet poster template
 *
 * Use for social image snippet
 * Logo and post title
 */

$poster = new PHPImage();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->resize(1200, 630, true, true);


// Change brightness and contrast
$filter = $poster->getResource();
imagefilter($filter, IMG_FILTER_CONTRAST, 45);
imagefilter($filter, IMG_FILTER_BRIGHTNESS, -85);
$poster->setResource($filter);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 40, 40);


// Draw title
$poster->setAlignVertical('center');
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');
$poster->setLineHeight(1.125);

if(!empty($textbox['title'])) {
    $poster->textBox($textbox['title'], [
        'x' => 40, 'y' => 140, 'width' => 1000, 'height' => 350, 'fontSize' => 36
    ]);
}

$poster->snapshot($basedir . $filename);
