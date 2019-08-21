<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Concerns;

use Illuminate\Support\Facades\Route;

class RouteTestRouter
{
    public function __construct()
    {
        Route::view('test', 'myview');
    }
}
