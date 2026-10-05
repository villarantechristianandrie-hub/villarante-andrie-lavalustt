<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class MigrationController extends Controller
{
    public function __construct()
    {
        // Browser access to migration routes is blocked in production.
        // The CLI (php lava migration ...) always works.
        if (PHP_SAPI !== 'cli' && strtolower(getenv('APP_ENV') ?: 'development') === 'production') {
            http_response_code(403);
            exit('Migration routes are disabled in production. Use: php lava migration run');
        }

        parent::__construct();
        $this->call->library('migration');
    }

    public function create_migration($migration_class)
    {
        $this->migration->create_migration($migration_class);
    }

    public function migrate()
    {
        $this->migration->migrate();
    }

    public function rollback()
    {
        $this->migration->rollback();
    }

    public function rollback_all()
    {
        $this->migration->rollback_all();
    }

    public function refresh()
    {
        $this->migration->refresh();
    }

    public function status()
    {
        $this->migration->status();
    }
}
