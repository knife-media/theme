<?php
/**
 * Quit smoking generator poster template
 *
 * Poster name: Как бросить курить
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);

// Set title font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');

// Draw title
$title = _x('Я знаю как бросить курить', 'generator: smoking', 'knife-theme');

$poster->text([
    'text' => $title,
    'x' => 88, 'y' => 320, 'width' => 600, 'height' => 100,
    'fontSize' => 28, 'lineHeight' => 1.5
]);

// Set title font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-regular.ttf');

// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => strip_tags($textbox['heading']),
        'x' => 88, 'y' => 380, 'width' => 1000, 'height' => 200,
        'lineHeight' => 1.5, 'fontSize' => 30
    ]);
}

$poster->snapshot($basedir . $filename);
