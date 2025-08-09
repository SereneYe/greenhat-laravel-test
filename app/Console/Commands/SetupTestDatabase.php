<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupTestDatabase extends Command
{
    protected $signature = 'test:setup-db {--force : Force the operation to run when in production}';
    protected $description = 'Setup test database with all migrations including module migrations';

    public function handle()
    {
        $this->info('Setting up test database...');

        // Set SQLite in-memory database configuration
        $this->info('Configuring SQLite in-memory database for testing');
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        // Check current database connection
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $this->info("Using database connection: {$connection}");
        $this->info("Database: {$database}");

        // Fresh migrations
        $this->info('Running fresh migrations...');
        $exitCode = Artisan::call('migrate:fresh', [
            '--env' => 'testing',
            '--force' => $this->option('force'),
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to run migrations');
            return $exitCode;
        }

        $this->info(Artisan::output());

        // Verify key tables exist
        $this->info('Verifying key tables...');
        $tables = [
            'users',
            'course_categories',
            'courses',
            'course_course_category',
            'course_enrollments',
        ];

        $missingTables = [];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✅ Table {$table} exists");
            } else {
                $this->error("❌ Table {$table} does not exist");
                $missingTables[] = $table;
            }
        }

        // Test factory functionality
        if (empty($missingTables)) {
            $this->info('Testing CourseCategory factory...');
            try {
                $category = \Modules\Course\Models\CourseCategory::factory()->create();
                $this->info("✅ Successfully created CourseCategory with ID: {$category->id}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to create CourseCategory: {$e->getMessage()}");
                return 1;
            }
        }

        if (empty($missingTables)) {
            $this->info('Test database setup completed successfully!');
            return 0;
        } else {
            $this->error('Test database setup completed with errors. Some tables are missing.');
            return 1;
        }
    }
}
