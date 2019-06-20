<?php
/**
 * Generic poster template
 *
 * Common template with all availible variables
 * Set moderate contrast and brightness
 */

$poster = new PHPImage();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->resize(1200, 630, true, true);


// Change brightness and contrast
$filter = $poster->getResource();
imagefilter($filter, IMG_FILTER_CONTRAST, 35);
imagefilter($filter, IMG_FILTER_BRIGHTNESS, -85);
$poster->setResource($filter);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 70, 40);

// Set font settings
$poster->setAlignVertical('top');
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');
$poster->setLineHeight(1.25);


// Draw description
if(!empty($textbox['description'])) {
    $poster->textBox($textbox['description'], [
        'x' => 70, 'y' => 445, 'width' => 950, 'height' => 120, 'fontSize' => 24
    ]);
}


// Draw title
if(!empty($textbox['title'])) {
    $poster->textBox($textbox['title'], [
        'x' => 70, 'y' => 160, 'width' => 950, 'height' => 200, 'fontSize' => 24
    ]);
}


// Draw heading
if(!empty($textbox['heading'])) {
    $poster->setLineHeight(1.125);
    $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');

    $poster->textBox($textbox['heading'], [
        'x' => 70, 'y' => 280, 'width' => 950, 'height' => 160, 'fontSize' => 52
    ]);
}

$poster->snapshot($basedir . $filename);
