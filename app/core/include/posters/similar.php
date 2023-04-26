<?php
/**
 * Increase description and title font size
 *
 * Poster name: Крупный шрифт описания
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


// Draw  title vertical line
$poster->rectangle( 190, 40, 2, 45, array( 255, 255, 255 ) );

// Draw post title
if ( ! empty( $textbox['title'] ) ) {
    $poster->text(
        array(
            'text'          => $textbox['title'],
            'x'             => 220,
            'y'             => 35,
            'width'         => 800,
            'height'        => 55,
            'fontSize'      => 24,
            'lineHeight'    => 1.5,
            'alignVertical' => 'center',
        )
    );
}

// Draw heading
if ( ! empty( $textbox['heading'] ) ) {
    $poster->text(
        array(
            'text'       => $textbox['heading'],
            'x'          => 40,
            'y'          => 220,
            'width'      => 950,
            'height'     => 140,
            'fontSize'   => 46,
            'lineHeight' => 1.5,
        ),
        $boundary
    );
}


// Draw description
if ( ! empty( $textbox['description'] ) ) {
    $y = 250;

    if ( isset( $boundary['height'] ) ) {
        $y = $y + $boundary['height'];
    }

    $poster->text(
        array(
            'text'       => $textbox['description'],
            'x'          => 40,
            'y'          => $y,
            'width'      => 950,
            'height'     => 580 - $y,
            'fontSize'   => 36,
            'lineHeight' => 1.625,
        )
    );
}


$poster->snapshot( $basedir . $filename );
