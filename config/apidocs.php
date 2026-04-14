<?php

return [

    /*
    |--------------------------------------------------------------------------
    | General documentation info
    |--------------------------------------------------------------------------
    |
    | This section contains basic documentation info
    |
    */
    'info' => [
        'version' => '1.0',
        'title' => env('APP_NAME', 'Laravel API documentation'),
        'description' => 'API documentation',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Company logo to show in the documentation
    |
    */
    'logo' => null,

    /*
    |--------------------------------------------------------------------------
    | Domain Address
    |--------------------------------------------------------------------------
    |
    */
    'domain' => env('APP_URL'),

    /*
    |--------------------------------------------------------------------------
    | Default exporter
    |--------------------------------------------------------------------------
    |
    | This package can export api documentation data with different format
    | if you would like.
    |
    | By default `Pneves001\Apidocs\Exporter` class will be used,
    | but feel free to use your own, if you would like to do so
    |
    */
    'exporter' => Pneves001\Apidocs\Exporter::class,

    /*
    |--------------------------------------------------------------------------
    | Apidocs header name
    |--------------------------------------------------------------------------
    |
    | Here you can set name of header added to request when `try it`
    | functionality is used
    |
    | Change it if default value interferes with your app
    |
    */
    'header_name' => 'x-apidocs',

    /*
    |--------------------------------------------------------------------------
    | Apidocs uri
    |--------------------------------------------------------------------------
    |
    | Uri address where documentation will be presented
    |
    */
    'uri' => env('APIDOCS_URI', '/apidocs'),

    /*
    |--------------------------------------------------------------------------
    | Json data file location
    |--------------------------------------------------------------------------
    |
    | Exported json file location
    |
    */
    'file_path' => env('APIDOCS_FILE_PATH', storage_path('apidocs.json')),

    /*
    |--------------------------------------------------------------------------
    | Markdown data file location
    |--------------------------------------------------------------------------
    |
    | Exported markdown file location (useful for AI)
    |
    */
    'markdown_file_path' => env('APIDOCS_MD_FILE_PATH', storage_path('apidocs.md')),

    /*
    |--------------------------------------------------------------------------
    | Directories
    |--------------------------------------------------------------------------
    |
    | Default directory names used for storing newly created classes
    | when `apidocs:endpoint` or `apidocs:param` are called.
    | Relatve to `app` directory
    |
    */
    'dir' => [
        'endpoints' => 'Apidocs/Endpoints',
        'params' => 'Apidocs/Params',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stacks
    |--------------------------------------------------------------------------
    |
    | If you want to support multiple docs stacks (e.g. external, internal)
    | you can define them here. If this array is empty, the default 
    | configuration above will be used.
    |
    */
    'stacks' => [
        // 'internal' => [
        //      'uri' => '/apidocs/internal',
        //      'file_path' => storage_path('apidocs-internal.json'),
        //      'markdown_file_path' => storage_path('apidocs-internal.md'),
        //      'info' => [
        //          'version' => '1.0',
        //          'title' => 'Internal API',
        //          'description' => 'Internal API documentation',
        //      ],
        // ]
    ],
];
