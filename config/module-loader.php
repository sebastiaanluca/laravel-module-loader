<?php

return [

    /*
     * Scan and autoload your modules at runtime.
     */
    'runtime_autoloading' => false,

    /*
     * An array of root project directories to scan for modules.
     */
    'directories' => [

        'modules',

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
