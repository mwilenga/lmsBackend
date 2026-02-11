<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {name}';
    protected $description = 'Create CRUD operations for a given model';

    public function handle()
    {
        $name = $this->argument('name');
        $lowerName = strtolower($name);

        // Create Controller
        $this->createController($name, $lowerName);

        // Create Model
        $this->createModel($name, $lowerName);

        // Create Service
        $this->createService($name, $lowerName);

        // Create Dao
        $this->createDao($name, $lowerName);

        // Create Route File
        $this->createRouteFile($lowerName, $name);

        // Create Migration
        $this->createMigration($lowerName);

        $this->info("CRUD for {$name} created successfully!");
    }

    protected function createController($name, $tableName)
    {
        $stub = File::get(__DIR__ . '/stubs/controller.stub');
        $stub = str_replace(
            ['{{name}}', '{{tableName}}', '{{lowerName}}'],
            [$name, $tableName, strtolower($name)],
            $stub
        );

        $path = app_path("Core/Controllers/{$name}Controller.php");

        $this->ensureDirectoryExists($path);
        File::put($path, $stub);
    }

    protected function createModel($name, $tableName)
    {
        $stub = File::get(__DIR__ . '/stubs/model.stub');
        $stub = str_replace(['{{name}}', '{{tableName}}'], [$name, $tableName], $stub);

        $path = app_path("Core/Model/{$name}.php");

        $this->ensureDirectoryExists($path);
        File::put($path, $stub);
    }

    protected function createService($name, $lowerName)
    {
        $stub = File::get(__DIR__ . '/stubs/service.stub');
        $stub = str_replace(['{{name}}', '{{lowerName}}'], [$name, $lowerName], $stub);

        $path = app_path("Core/Services/{$name}Service.php");

        $this->ensureDirectoryExists($path);
        File::put($path, $stub);
    }

    protected function createDao($name, $lowerName)
    {
        $stub = File::get(__DIR__ . '/stubs/dao.stub');
        $stub = str_replace(['{{name}}', '{{lowerName}}'], [$name, $lowerName], $stub);

        $path = app_path("Core/Dao/{$name}Dao.php");

        $this->ensureDirectoryExists($path);
        File::put($path, $stub);
    }

    protected function createRouteFile($lowerName, $name)
    {
        $stub = File::get(__DIR__ . '/stubs/routes.stub');
        $stub = str_replace(['{{name}}', '{{lowerName}}'], [$name, $lowerName], $stub);

        // Create route file directly in Core/Routes
        $routesDirectory = app_path('Core/Routes');
        $path = "{$routesDirectory}/{$lowerName}.php";

        // Ensure directory exists
        if (!File::exists($routesDirectory)) {
            File::makeDirectory($routesDirectory, 0755, true);
        }

        File::put($path, $stub);

        $this->appendToRoutesFile();
    }

    protected function appendToRoutesFile()
    {
        $routesFile = base_path('routes/api.php');
        $globalLoader = "\n// Auto-loaded Core Routes\nforeach (glob(__DIR__.'/../app/Core/Routes/*.php') as \$routeFile) {\n    require \$routeFile;\n}\n";

        // Check if we need to add the loader
        $currentContent = File::get($routesFile);

        if (!str_contains($currentContent, "glob(__DIR__.'/../app/Core/Routes/*.php')")) {
            // Add the global loader at the end
            File::append($routesFile, $globalLoader);
        }
    }

    protected function createMigration($tableName)
    {
        $migrationName = "create_{$tableName}_table";
        $migrationPath = database_path("migrations/" . date('Y_m_d_His') . "_{$migrationName}.php");

        $stub = File::get(__DIR__ . '/stubs/migration.stub');
        $stub = str_replace('{{tableName}}', $tableName, $stub);

        $this->ensureDirectoryExists($migrationPath);
        File::put($migrationPath, $stub);
    }

    protected function ensureDirectoryExists($path)
    {
        $dir = dirname($path);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
    }
}
