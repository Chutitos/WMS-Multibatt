<?php

namespace App\Providers;

use App\Services\Erp\DefontanaClient;
use App\Services\Erp\ErpClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // El WMS habla con el ERP solo a través del contrato ErpClient;
        // cambiar de proveedor o conectar la API real es cambiar este bind.
        $this->app->bind(ErpClient::class, DefontanaClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
