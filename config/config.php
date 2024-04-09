<?php

require_once __DIR__.'/vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

// local auto var scope to avoid monkeying with the global namespace
if (!isset($dotenvLoader)) {
    $dotenvLoader = new Dotenv();
    $dotenvLoader->loadEnv(__DIR__.'/.env');

    if (class_exists('\Sentry')) {
        \Sentry\init([
            'dsn'=>'https://6b1e5bd7774f4953b9b4c2cd59947f41@o139954.ingest.sentry.io/6077567',
            'environment'=>$_ENV['APP_ENV'] ?? 'unknown'
        ]);
    }

    empty($_ENV['WP_SITEURL']) || define('WP_SITEURL', $_ENV['WP_SITEURL']);
    empty($_ENV['WP_HOME']) || define('WP_HOME', $_ENV['WP_HOME']);

    define('WP_DEBUG', filter_var($_ENV['WP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
    define('WP_DEBUG_DISPLAY', filter_var($_ENV['WP_DEBUG_DISPLAY'] ?? false, FILTER_VALIDATE_BOOLEAN));

    $debugLog = $_ENV['WP_DEBUG_LOG'] ?? false;
    $debugLog = filter_var($debugLog, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $debugLog;

    if ($debugLog === true || is_string($debugLog)) {
        define('WP_DEBUG_LOG', $debugLog);
    }

    define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');
    define('DB_COLLATE', $_ENV['DB_COLLATE'] ?? '');
    define('DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
    define('DB_NAME', $_ENV['DB_NAME'] ?? 'wordpress');
    define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
    define('DB_USER', $_ENV['DB_USER'] ?? 'wordpress');

    define('DISABLE_WP_CRON', $_ENV['DISABLE_WP_CRON'] ?? false);
    define('MEDIA_TRASH', $_ENV['MEDIA_TRASH'] ?? true);
    define('FS_METHOD', 'direct');

    define('AUTH_KEY', $_ENV['AUTH_KEY']);
    define('AUTH_SALT', $_ENV['AUTH_SALT']);
    define('LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY']);
    define('LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT']);
    define('NONCE_KEY', $_ENV['NONCE_KEY']);
    define('NONCE_SALT', $_ENV['NONCE_SALT']);
    define('SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY']);
    define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);

    define('WP_POST_REVISIONS', 10);
    define('DISALLOW_FILE_EDIT', true);
    define('WP_AUTO_UPDATE_CORE', true);
}
