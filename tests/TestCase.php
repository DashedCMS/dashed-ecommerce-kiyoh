<?php

namespace Dashed\DashedEcommerceKiyoh\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Eloquent\Factories\Factory;
use Dashed\DashedEcommerceKiyoh\DashedEcommerceKiyohServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Dashed\\DashedEcommerceKiyoh\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            DashedEcommerceKiyohServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_dashed-ecommerce-kiyoh_table.php.stub';
        $migration->up();
        */
    }
}
