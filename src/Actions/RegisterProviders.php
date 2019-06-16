<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

class RegisterProviders
{
    /**
     * @param array $providers
     *
     * @return void
     */
    public function execute(array $providers) : void
    {
        foreach ($providers as $provider) {
            app()->register($provider);
        }
    }
}
