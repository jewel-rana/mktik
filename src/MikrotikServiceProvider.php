<?php
namespace Rajtika\Mikrotik;
require_once __DIR__ . '/Libs/mikrotik/core/mapi_routerosapi.php';
require_once  __DIR__ . '/Libs/pear2/vendor/autoload.php';

use Illuminate\Support\ServiceProvider;
use Rajtika\Mikrotik\Services\Mikrotik;
use Rajtika\Mikrotik\Services\Routeros;

class MikrotikServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('routeros', function () {
            return new Routeros();
        });
        $this->app->bind('mikrotik', function () {
            return new Mikrotik();
        });
        $this->publishes([
            __DIR__.'/config/mikrotik.php' =>  config_path('mikrotik.php'),
        ], 'config');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
