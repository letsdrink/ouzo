<?php
use Ouzo\Db\Dialect\MySqlDialect;
use Ouzo\Db\Dialect\PostgresDialect;
use Ouzo\Db\Dialect\Sqlite3Dialect;

$db = getenv('db');
if ($db == 'mysql') {
    $config['sql_dialect'] = MySqlDialect::class;
    $config['db']['user'] = 'travis';
    $config['db']['pass'] = '';
    $config['db']['driver'] = 'mysql';
    $config['db']['host'] = '127.0.0.1';
    $config['db']['port'] = '3306';
} else if ($db == 'sqlite3') {
    $config['sql_dialect'] = Sqlite3Dialect::class;
    $config['db']['driver'] = 'sqlite';
    $config['db']['dsn'] = 'sqlite:ouzo_test';
} else {
    $config['sql_dialect'] = PostgresDialect::class;
    $config['db']['user'] = 'ouzo_user';
    $config['db']['pass'] = 'password';
    $config['db']['driver'] = 'pgsql';
    $config['db']['host'] = '172.17.0.3';
    $config['db']['port'] = '5432';
}
$config['db']['dbname'] = 'ouzo';

$config['global']['prefix_system'] = '';
$config['global']['suffix_cache'] = '1234';
$config['default']['auth'] = 'Database';
$config['debug'] = true;
$config['language'] = 'en';

return $config;
