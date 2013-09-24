<?php
$config['db']['dbname'] = 'ouzo_test';
$config['db']['user'] = 'postgres';
$config['db']['pass'] = '';
$config['db']['driver'] = 'pgsql';
$config['db']['host'] = '127.0.0.1';
$config['db']['port'] = '5432';
$config['global']['controller'] = 'index';
$config['global']['action'] = 'index';
$config['global']['prefix_system'] = '/panel/panel2.0';
$config['global']['suffix_cache'] = '1234';
$config['default']['auth'] = 'Database';
$config['debug'] = true;
$config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\PostgresDialect';

return $config;
