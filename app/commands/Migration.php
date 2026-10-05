<?php
/**
 * Command: Migration
 * Usage: php lava migration [run|create-migration|rollback|rollback-all|refresh|status] [name]
 */
class Migration
{
    public static $command = 'migration';

    public static $description = 'Run database migrations';

    public static $arguments = [
        '[action]' => 'Action: run, create-migration, rollback, rollback-all, refresh, status',
        '[name]'   => 'Migration class name for create-migration',
    ];

    protected static $route_map = [
        'run'              => 'migrate',
        'create-migration' => 'create-migration',
        'rollback'         => 'rollback',
        'rollback-all'     => 'rollback-all',
        'refresh'          => 'refresh',
        'status'           => 'status',
    ];

    /**
     * The LavaLust CLI calls handle($input, $flags), where $input is the
     * first positional argument (the action). The migration name is the
     * second positional argument, so we read it from $argv.
     */
    public function handle($action = null, array $flags = [])
    {
        global $argv;
        $name   = $argv[3] ?? null;
        $action = $action ?? 'run';

        if (!isset(static::$route_map[$action])) {
            echo danger("Unknown migration action: \"{$action}\"");
            echo "Available actions: " . implode(', ', array_keys(static::$route_map)) . PHP_EOL;
            exit(1);
        }

        if ($action === 'create-migration') {
            if (!$name) {
                echo danger("Migration name is required.");
                echo "Example: php lava migration create-migration create_users_table" . PHP_EOL;
                exit(1);
            }
            $route = 'create-migration/' . $name;
        } else {
            $route = static::$route_map[$action];
        }

        $index = PUBLIC_DIR . 'index.php';
        if (!file_exists($index)) {
            echo danger("index.php not found at: {$index}");
            exit(1);
        }

        passthru(sprintf('php %s %s', escapeshellarg($index), escapeshellarg($route)));
    }
}
