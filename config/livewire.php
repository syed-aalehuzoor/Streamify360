<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | This value is the base URL for Livewire's JavaScript assets. It's used when
    | generating URLs to scripts and stylesheets. You can use a full URL or a
    | relative path, depending on your setup.
    |
    */
    'asset_url' => null,

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | These options are used to configure file upload behavior in Livewire.
    |
    */
    'temporary_file_upload' => [
        'rules' => 'max:512000', // (100MB max, and only pngs, jpegs, and pdfs.)
    ],

    /*
    |--------------------------------------------------------------------------
    | Rendering Configuration
    |--------------------------------------------------------------------------
    |
    | Configure whether or not Livewire components should be rendered within a
    | specific view file layout.
    |
    */
    'render_on_redirect' => false,
];
