<?php
/**
 * Gothic poster template
 *
 * Poster name: Готика
 * Target: generator
 */

$poster = new PHPImage();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->resize(1200, 630, true, true);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 40, 40);

// Set font settings
$poster->setAlignVertical('top');
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');


// Draw title
$heading = _x('Темная нейросеть «Ножа» рекомендует:', 'generator: gothic', 'knife-theme');

$poster->setLineHeight(1.25);
$poster->textBox(mb_strtoupper($heading), [
    'x' => 40, 'y' => 160, 'width' => 600, 'height' => 100, 'fontSize' => 28
]);


// Draw heading
if(!empty($textbox['heading'])) {
    $poster->setLineHeight(1.075);
    $poster->textBox(mb_strtoupper($textbox['heading']), [
        'x' => 40, 'y' => 290, 'width' => 950, 'height' => 250, 'fontSize' => 42, 'fontColor' => [253, 8, 64]
    ]);
}

$poster->snapshot($basedir . $filename);
