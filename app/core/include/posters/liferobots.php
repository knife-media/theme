<?php
/**
 * Life after robots poster template
 *
 * Poster name: Life after robots
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);


// Set heading font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');


// Draw heading
if(!empty($textbox['description'])) {
    $poster->text([
        'text' => mb_strtoupper($textbox['description']),
        'x' => 60, 'y' => 370, 'width' => 1000, 'height' => 200,
        'lineHeight' => 1.5, 'fontSize' => 54
    ]);
}

$poster->snapshot($basedir . $filename);
