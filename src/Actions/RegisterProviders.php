<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Actions;

class RegisterProviders
{
    /**
     * @var array
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return void
     */
    public function execute() : void
    {
        foreach ($this->providers as $provider) {
            app()->register($provider);
        }
    }
}
