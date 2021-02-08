<?php
/**
 * Good news generator poster template
 *
 * Poster name: Хорошие новости
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);

// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 60, 60);

// Set title font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');

// Draw title
$title = _x('Представляете?', 'generator: good-news', 'knife-theme');

$poster->text([
    'text' => $title,
    'x' => 60, 'y' => 240, 'width' => 600, 'height' => 100,
    'fontSize' => 28, 'lineHeight' => 1.5
]);

// Set title font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');

// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => strip_tags($textbox['heading']),
        'x' => 60, 'y' => 320, 'width' => 1000, 'height' => 300,
        'lineHeight' => 1.5, 'fontSize' => 42
    ]);
}

$poster->snapshot($basedir . $filename);
