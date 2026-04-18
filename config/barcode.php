<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Barcode Settings
    |--------------------------------------------------------------------------
    |
    | These are the system-wide default settings for barcode overlay.
    | Users can override these with their own preferences.
    |
    */

    'defaults' => [
        'x_percent' => 5.0,
        'y_percent' => 3.0,
        'width_percent' => 25.0,
        'height_percent' => 5.0,
        'show_text' => true,
        'page' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Barcode Position Presets
    |--------------------------------------------------------------------------
    |
    | Predefined barcode positions for quick selection.
    | Each preset includes x, y, width, and height percentages.
    |
    */

    'presets' => [
        'top-left' => [
            'name' => 'Top Left',
            'description' => 'Position barcode at the top left corner',
            'icon' => 'corner-up-left',
            'x_percent' => 5.0,
            'y_percent' => 3.0,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'top-right' => [
            'name' => 'Top Right',
            'description' => 'Position barcode at the top right corner',
            'icon' => 'corner-up-right',
            'x_percent' => 70.0,
            'y_percent' => 3.0,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'top-center' => [
            'name' => 'Top Center',
            'description' => 'Position barcode at the top center',
            'icon' => 'arrow-up',
            'x_percent' => 37.5,
            'y_percent' => 3.0,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'bottom-left' => [
            'name' => 'Bottom Left',
            'description' => 'Position barcode at the bottom left corner',
            'icon' => 'corner-down-left',
            'x_percent' => 5.0,
            'y_percent' => 92.0,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'bottom-right' => [
            'name' => 'Bottom Right',
            'description' => 'Position barcode at the bottom right corner',
            'icon' => 'corner-down-right',
            'x_percent' => 70.0,
            'y_percent' => 92.0,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'bottom-center' => [
            'name' => 'Bottom Center',
            'description' => 'Position barcode at the bottom center',
            'icon' => 'arrow-down',
            'x_percent' => 37.5,
            'y_percent' => 92.0,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'center' => [
            'name' => 'Center',
            'description' => 'Position barcode at the center of the page',
            'icon' => 'maximize-2',
            'x_percent' => 37.5,
            'y_percent' => 47.5,
            'width_percent' => 25.0,
            'height_percent' => 5.0,
        ],
        'small-top-right' => [
            'name' => 'Small Top Right',
            'description' => 'Compact barcode at top right',
            'icon' => 'minimize-2',
            'x_percent' => 80.0,
            'y_percent' => 2.0,
            'width_percent' => 18.0,
            'height_percent' => 4.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Barcode Validation Rules
    |--------------------------------------------------------------------------
    |
    | Validation constraints for barcode position and size.
    |
    */

    'validation' => [
        'x_percent' => ['min' => 0, 'max' => 100],
        'y_percent' => ['min' => 0, 'max' => 100],
        'width_percent' => ['min' => 5, 'max' => 100],
        'height_percent' => ['min' => 2, 'max' => 50],
        'page' => ['min' => 0],
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported File Types
    |--------------------------------------------------------------------------
    |
    | File types that support barcode overlay.
    |
    */

    'supported_types' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'],

];
