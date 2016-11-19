<?php
$db = getenv('db');
if ($db == 'mysql') {
    $config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\MySqlDialect';
    $config['db']['user'] = 'travis';
    $config['db']['pass'] = '';
    $config['db']['driver'] = 'mysql';
    $config['db']['host'] = '127.0.0.1';
    $config['db']['port'] = '3306';
} else if ($db == 'sqlite3') {
    $config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\Sqlite3Dialect';
    $config['db']['driver'] = 'sqlite';
    $config['db']['dsn'] = 'sqlite:ouzo_test';
} else {
    $config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\PostgresDialect';
    $config['db']['user'] = 'postgres';
    $config['db']['pass'] = '';
    $config['db']['driver'] = 'pgsql';
    $config['db']['host'] = '127.0.0.1';
    $config['db']['port'] = '5432';
}
$config['db']['dbname'] = 'ouzo_test';

$config['global']['prefix_system'] = '';
$config['global']['prefix_application'] = '';
$config['global']['suffix_cache'] = '1234';
$config['default']['auth'] = 'Database';
$config['debug'] = true;
$config['language'] = 'en';

return $config;
