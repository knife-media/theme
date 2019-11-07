<?php
/**
 * Less description template
 *
 * Poster name: Постер без описания
 * Target: quiz
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);


// Change brightness and contrast
$filter = $poster->getResource();
imagefilter($filter, IMG_FILTER_CONTRAST, 35);
imagefilter($filter, IMG_FILTER_BRIGHTNESS, -85);
$poster->setResource($filter);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 40, 40);

// Set font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');


// Draw  title vertical line
$poster->rectangle(190, 40, 2, 45, [255, 255, 255]);

// Draw post title
if(!empty($textbox['title'])) {
    $poster->text([
        'text' => $textbox['title'],
        'x' => 220, 'y' => 35, 'width' => 800, 'height' => 55,
        'fontSize' => 24, 'lineHeight' => 1.5, 'alignVertical' => 'center'
    ]);
}

// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => $textbox['heading'],
        'x' => 40, 'y' => 140, 'width' => 950, 'height' => 400,
        'alignVertical' => 'center', 'fontSize' => 52, 'lineHeight' => 1.375
    ], $boundary);
}


$poster->snapshot($basedir . $filename);
