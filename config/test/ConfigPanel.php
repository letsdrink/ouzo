<?php
$config['db']['dbname'] = 'framework_test';
$config['db']['user'] = 'user';
$config['db']['pass'] = 'pass';
$config['db']['driver'] = 'pgsql';
$config['db']['host'] = '127.0.0.1';
$config['db']['port'] = '5432';
$config['global']['controller'] = 'index';
$config['global']['action'] = 'index';
$config['global']['prefix_system'] = '/panel';
$config['global']['suffix_cache'] = '1234';
$config['default']['auth'] = 'Database';
$config['debug'] = true;

return $config;
