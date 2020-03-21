<?php
/**
 * Poetry poster template
 *
 * Poster name: Поэзия
 * Target: generator
 */

$poster = new ImageText();
$poster->setDimensionsFromImage($image)->draw($image);
$poster->setOutput('jpg');
$poster->crop(1200, 630, true);


// Draw logo image
$poster->draw(get_template_directory() . '/assets/images/logo-title.png', 40, 40);

// Set font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');


// Draw heading
if(!empty($textbox['heading'])) {
    $poster->text([
        'text' => $textbox['heading'],
        'x' => 40, 'y' => 30, 'width' => 760, 'height' => 200,
        'lineHeight' => 1.5, 'fontSize' => 26, 'fontColor' => [250, 246, 0],
        'alignVertical' => 'bottom'
    ]);
}

// Set updated font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-medium.ttf');


// Draw description
if(!empty($textbox['description'])) {
    $text = explode("\n\n", $textbox['description'], 2);

    if(!empty($text[0])) {
        $poster->text([
            'text' => $text[0],
            'x' => 40, 'y' => 270, 'width' => 1100, 'height' => 400,
            'lineHeight' => 1.5, 'fontSize' => 30
        ], $boundary);
    }

    if(!empty($text[1])) {
        $y = $boundary['height'] + 300;

        $poster->text([
            'text' => $text[1],
            'x' => 40, 'y' => $y, 'width' => 1100, 'height' => 400,
            'lineHeight' => 1.5, 'fontSize' => 24
        ], $boundary);
    }
}


// Set heading font settings
$poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-bold.ttf');

$poster->snapshot($basedir . $filename);
