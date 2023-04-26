<?php
/**
 * Snippet poster template
 *
 * Poster name: Сниппет для соцсетей
 * Target: social
 */

$poster = new ImageText();
$poster->setDimensionsFromImage( $image )->draw( $image );
$poster->setOutput( 'jpg' );
$poster->crop( 1200, 630, true );


// Change brightness and contrast
$filter = $poster->getResource();
imagefilter( $filter, IMG_FILTER_CONTRAST, 45 );
imagefilter( $filter, IMG_FILTER_BRIGHTNESS, -85 );
$poster->setResource( $filter );


// Draw logo image
$poster->draw( get_template_directory() . '/assets/images/logo-title.png', 40, 40 );


// Draw title
$poster->setAlignVertical( 'center' );
$poster->setFont( get_template_directory() . '/assets/fonts/formular/formular-medium.ttf' );

if ( ! empty( $textbox['title'] ) ) {
    $poster->text(
        array(
            'text'       => $textbox['title'],
            'x'          => 40,
            'y'          => 140,
            'width'      => 1000,
            'height'     => 350,
            'fontSize'   => 36,
            'lineHeight' => 1.5,
        )
    );
}

$poster->snapshot( $basedir . $filename );
