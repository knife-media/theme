<?php
/**
 * Last of us poster template
 *
 * Poster name: Last of us
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);


// Set font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');


// Draw kicker
$poster->text([
    'text' => _x("Генератор скиллов,\nкоторые вам надо срочно освоить", 'poster: lastofus', 'knife-theme'),
    'x' => 80, 'y' => 210, 'width' => 1100, 'height' => 360,
    'lineHeight' => 1.5, 'fontSize' => 22
]);


// Set heading font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');


// Draw heading
$poster->text([
    'text' => mb_strtoupper(_x("Мне пора", 'poster: lastofus', 'knife-theme')),
    'x' => 75, 'y' => 310, 'width' => 1100, 'height' => 360,
    'lineHeight' => 1.5, 'fontSize' => 78, 'fontColor' => [249, 215, 181],
]);


// Draw title
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => mb_strtoupper($textbox['heading']),
        'x' => 80, 'y' => 410, 'width' => 1000, 'height' => 200,
        'lineHeight' => 1.5, 'fontSize' => 36, 'fontColor' => [249, 215, 181]
    ]);
}

$poster->snapshot($basedir . $filename);
