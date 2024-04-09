<?php

$wpdb->save_queries = false;
$wpdb->persistent = false;
$wpdb->max_connections = 30;
$wpdb->check_tcp_responsiveness = true;

$wpdb->add_database([
    'host'=>$_ENV['DB_HOST_WRITER'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
    'user'=>DB_USER,
    'password'=>DB_PASSWORD,
    'name'=>DB_NAME,
    'write'=>1,
    'read'=>1,
]);

$wpdb->add_database([
    'host'=>$_ENV['DB_HOST_READER'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
    'user'=>DB_USER,
    'password'=>DB_PASSWORD,
    'name'=>DB_NAME,
    'write'=>0,
    'read'=>1,
    'dataset'=>'global',
    'timeout'=>0.2,
]);
