<?php
$config['db']['dbname'] = 'framework_test';
$config['db']['user'] = 'thulium_1';
$config['db']['pass'] = 'a';
$config['db']['driver'] = 'pgsql';
$config['db']['host'] = '127.0.0.1';
$config['db']['port'] = '5432';
$config['global']['controller'] = 'index';
$config['global']['action'] = 'index';
$config['global']['prefix_system'] = '/panel/panel2.0';
$config['global']['suffix_cache'] = '1234';
$config['default']['auth'] = 'Database';
$config['debug'] = true;

return $config;