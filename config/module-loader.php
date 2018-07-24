<?php

return [

    /*
     * An array of paths to scan for modules.
     */
    'paths' => [

        base_path('modules'),

    ],

    /*
     * Eloquent factories should only be registered in local development
     * environments. You can add your production environment to enable
     * them there too.
     */
    'development_environments' => [
        'local',
        'dev',
        'development',
        'testing',
    ],

];
