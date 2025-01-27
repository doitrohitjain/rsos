<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |    
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |    
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timout:
    |    
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */
    'pdf' => [
        'enabled' => true,
        'binary'  => '"C:\wkhtmltox\bin\wkhtmltopdf"',
        'timeout' => false,
        'options' => array(
		 'enable-local-file-access' => true,
        'encoding' => 'utf-8'
            ),
        'env'     => [],
    ],
    
    'image' => [
        'enabled' => true,
        'binary'  => '"C:\wkhtmltox\bin\wkhtmltopdf"',
        'timeout' => 60000,
        'options' => array(
		'enable-local-file-access' => true,
        'encoding' => 'utf-8',
            
        ),
        'options' => [
            'print-media-type' => true,
            'image-dpi' => '600',
            'load-error-handling' => 'ignore',
            'load-media-error-handling' => 'ignore',
        ],
        'env'     => [], 
    ],

];





