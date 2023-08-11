<?php

use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Statix\FormAction\FormActionServiceProvider;
use Statix\FormAction\Tests\Support\Controller;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            FormActionServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory().DIRECTORY_SEPARATOR.'database.sqlite',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabase(): void
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
        });
    }

    protected function getTempDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'temp';
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->post('/test', [Controller::class, 'store']);
    }
}

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);
