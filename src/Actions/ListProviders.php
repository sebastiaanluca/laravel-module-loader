<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

use SebastiaanLuca\Module\Entities\Module;

class ListProviders
{
    /**
     * Get the service provider of each module and return all.
     *
     * @param array $modules
     *
     * @return array
     */
    public function execute(array $modules) : array
    {
        return collect($modules)
            ->map(function (Module $module) {
                return (new DetermineProvider($module))->execute();
            })
            ->reject(null)
            ->all();
    }
}
