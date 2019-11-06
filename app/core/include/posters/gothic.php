<?php
/**
 * Gothic poster template
 *
 * Poster name: Готика
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->crop(1200, 630, true);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 40, 40);

// Set font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');


// Draw title
$title = _x('Темная нейросеть «Ножа» рекомендует:', 'generator: gothic', 'knife-theme');

$poster->text([
    'text' => mb_strtoupper($title),
    'x' => 40, 'y' => 160, 'width' => 600, 'height' => 100,
    'fontSize' => 28, 'lineHeight' => 1.25
]);


// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => mb_strtoupper($textbox['heading']),
        'x' => 40, 'y' => 290, 'width' => 950, 'height' => 250,
        'lineHeight' => 1.075, 'fontSize' => 42, 'fontColor' => [253, 8, 64]
    ]);
}

$poster->snapshot($basedir . $filename);
