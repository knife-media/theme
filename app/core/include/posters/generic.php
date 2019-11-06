<?php
/**
 * Quiz poster template
 *
 * Poster name: Универсальный шаблон
 * Target: quiz
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
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
        'x' => 220, 'y' => 42, 'width' => 800, 'height' => 45,
        'fontSize' => 24, 'lineHeight' => 1
    ]);
}

// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => $textbox['heading'],
        'x' => 40, 'y' => 240, 'width' => 950, 'height' => 160,
        'fontSize' => 38, 'lineHeight' => 1.125
    ], $boundary);
}


// Draw description
if(!empty($textbox['description'])) {
    $y = 270;

    if(isset($boundary['height'])) {
        $y = $y + $boundary['height'];
    }

    $poster->text([
        'text' => $textbox['description'],
        'x' => 40, 'y' => $y, 'width' => 950, 'height' => 200,
        'fontSize' => 24, 'lineHeight' => 1.125
    ]);
}


$poster->snapshot($basedir . $filename);
