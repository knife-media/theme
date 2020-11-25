<?php
/**
 * Sand generator poster template
 *
 * Poster name: Песчаные карьеры
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);

// Change brightness and contrast
$filter = $poster->getResource();
imagefilter($filter, IMG_FILTER_CONTRAST, 25);
imagefilter($filter, IMG_FILTER_BRIGHTNESS, -50);
$poster->setResource($filter);


// Set title font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');

// Draw title
$title = _x('Я обожаю песчаные карьеры, мой любимый песчаный карьер:', 'generator: sand', 'knife-theme');

$poster->text([
    'text' => $title,
    'x' => 60, 'y' => 280, 'width' => 600, 'height' => 100,
    'fontSize' => 28, 'lineHeight' => 1.5
]);

// Set title font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');

// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => strip_tags($textbox['heading']),
        'x' => 60, 'y' => 380, 'width' => 1000, 'height' => 200,
        'lineHeight' => 1.5, 'fontSize' => 52
    ]);
}

$poster->snapshot($basedir . $filename);
