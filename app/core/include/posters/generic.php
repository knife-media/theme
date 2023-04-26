<?php
/**
 * Long entry title template
 *
 * Poster name: Длинный заголовок записи
 * Target: quiz
 */

$poster = new ImageText();
$poster->setDimensionsFromImage( $image )->draw( $image );
$poster->setOutput( 'jpg' );
$poster->crop( 1200, 630, true );


// Change brightness and contrast
$filter = $poster->getResource();
imagefilter( $filter, IMG_FILTER_CONTRAST, 35 );
imagefilter( $filter, IMG_FILTER_BRIGHTNESS, -85 );
$poster->setResource( $filter );


// Draw logo image
$poster->draw( get_template_directory() . '/assets/images/logo-title.png', 40, 40 );

// Set font settings
$poster->setFont( get_template_directory() . '/assets/fonts/formular/formular-medium.ttf' );


// Draw post title
if ( ! empty( $textbox['title'] ) ) {
    $poster->text(
        array(
            'text'          => $textbox['title'],
            'x'             => 40,
            'y'             => 180,
            'width'         => 800,
            'height'        => 60,
            'fontSize'      => 24,
            'lineHeight'    => 1.5,
            'verticalAlign' => 'center',
        ),
        $boundary
    );

    // Draw  title vertical line
    $poster->rectangle( 40, $boundary['height'] + 210, 950, 2, array( 255, 255, 255 ) );
}


// Draw heading
if ( ! empty( $textbox['heading'] ) ) {
    $y = 245;

    if ( isset( $boundary['height'] ) ) {
        $y = $y + $boundary['height'];
    }

    $poster->text(
        array(
            'text'       => $textbox['heading'],
            'x'          => 40,
            'y'          => $y,
            'width'      => 950,
            'height'     => 140,
            'fontSize'   => 38,
            'lineHeight' => 1.5,
        ),
        $boundary
    );
}


// Draw description
if ( ! empty( $textbox['description'] ) ) {
    $y = 265;

    if ( isset( $boundary['height'] ) ) {
        $y = $y + $boundary['height'];
    }

    $poster->text(
        array(
            'text'       => $textbox['description'],
            'x'          => 40,
            'y'          => $y,
            'width'      => 950,
            'height'     => 140,
            'fontSize'   => 24,
            'lineHeight' => 1.5,
        )
    );
}


$poster->snapshot( $basedir . $filename );
