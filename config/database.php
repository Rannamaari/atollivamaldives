<?php

use Illuminate\Support\Str;
use Pdo\Mysql;

$mysqlAttr = static function (string $newConstant, string $legacyConstant): int|string {
    return class_exists(Mysql::class)
        ? constant(Mysql::class.'::'.$newConstant)
        : constant(PDO::class.'::'.$legacyConstant);
};

$mysqlOptions = array_filter([
    $mysqlAttr('ATTR_SSL_CA', 'MYSQL_ATTR_SSL_CA') => env('DB_SSL_CA'),
    $mysqlAttr('ATTR_SSL_CAPATH', 'MYSQL_ATTR_SSL_CAPATH') => env('DB_SSL_CAPATH'),
    $mysqlAttr('ATTR_SSL_CERT', 'MYSQL_ATTR_SSL_CERT') => env('DB_SSL_CERT'),
    $mysqlAttr('ATTR_SSL_KEY', 'MYSQL_ATTR_SSL_KEY') => env('DB_SSL_KEY'),
    $mysqlAttr('ATTR_SSL_VERIFY_SERVER_CERT', 'MYSQL_ATTR_SSL_VERIFY_SERVER_CERT') => env('DB_SSL_VERIFY_SERVER_CERT', true),
], fn ($value) => filled($value) || is_bool($value));

return [
    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('DB_SCHEMA', 'public'),
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'micro_travel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => $mysqlOptions,
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
    ],
];
