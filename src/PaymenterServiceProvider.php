<?php

namespace M74asoud\Paymenter;

use Illuminate\Support\ServiceProvider;

class PaymenterServiceProvider extends ServiceProvider {

    public function boot() {
        //dump('paymenter loaded');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        $this->publishes( [
            __DIR__ . '/Config/m74_paymenter.php'           => config_path( 'm74_paymenter.php' ),
            __DIR__ . '/Database/Factories' => database_path( 'factories' ),
            __DIR__ . '/Database/Migrations' => database_path( 'migrations' ),
        ] );

        //$this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

    }

    public function register() {
    }
}
