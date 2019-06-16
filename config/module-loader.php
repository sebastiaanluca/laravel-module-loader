<?php

return [

    /*
     * An array of project directories to scan for modules.
     */

    'directories' => [
        'modules',
    ],

    /*
     * Optionally prefix module namespaces to group them under a parent namespace.
     */

    'namespaces' => [
        'modules' => null,
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
