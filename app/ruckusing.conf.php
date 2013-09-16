<?php
$config = require_once RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'config/' . getenv('environment') . '/ConfigPanel.php';

return array(
    'db' => array(
        'development' => array(
            'type' => $config['db']['driver'],
            'host' => $config['db']['host'],
            'port' => $config['db']['port'],
            'database' => $config['db']['dbname'],
            'user' => $config['db']['user'],
            'password' => $config['db']['pass'],
            'directory' => ''
        ),
        'test' => array(
            'type' => $config['db']['driver'],
            'host' => $config['db']['host'],
            'port' => $config['db']['port'],
            'database' => $config['db']['dbname'],
            'user' => $config['db']['user'],
            'password' => $config['db']['pass'],
            'directory' => ''
        )
    ),
    'migrations_dir' => array('default' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db/migrations'),
    'db_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db',
    'log_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db/logs',
    'ruckusing_base' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'ruckusing' . DIRECTORY_SEPARATOR . 'ruckusing-migrations'
);