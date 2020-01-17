<?php
/**
 * Astrology poster template
 *
 * Poster name: Гадалка
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 40, 40);

// Set font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');


// Draw heading
if(!empty($textbox['description'])) {
    $poster->text([
        'text' => $textbox['description'],
        'x' => 40, 'y' => 200, 'width' => 1100, 'height' => 360,
        'lineHeight' => 1.5, 'fontSize' => 27,
        'alignVertical' => 'bottom'
    ], $boundary);
}


// Set heading font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');


// Draw heading
if(!empty($textbox['heading'])) {
    $y = 200;

    if(isset($boundary['height'])) {
        $y = 340 - $boundary['height'];
    }

    $poster->text([
        'text' => mb_strtoupper($textbox['heading']),
        'x' => 40, 'y' => $y, 'width' => 1100, 'height' => 200,
        'lineHeight' => 1.5, 'fontSize' => 34, 'fontColor' => [255, 243, 78],
        'alignVertical' => 'bottom'
    ]);
}

$poster->snapshot($basedir . $filename);
