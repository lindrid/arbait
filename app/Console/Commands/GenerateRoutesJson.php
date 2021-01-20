<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateRoutesJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:json';

    protected $router;

    /**
     * Команда создает файл resources/js/routes.json
     * для того, чтобы был доступ ко всем
     * путям именно из js файлов, т.е. из front-end'а
     *
     * @var string
     */
    protected $description = 'Create file resources/js/routes.json that will store all routes'.
        'for using them in JS scripts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $routes = array();
        foreach ($this->router->getRoutes() as $route) {
            $name = $route->getName();
            $routes[$name] = 'http://arbait/' . $route->uri();
        }

        File::put('resources/js/routes.json', json_encode($routes,JSON_PRETTY_PRINT));
    }
}
