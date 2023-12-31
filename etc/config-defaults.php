<?php

/**
 * DO NOT EDIT THIS FILE IT MIGHT BE UPDATED
 * 
 * Put your own settings in config.php that will be included automatically
 * 
 */

$config = [
    'token'     => 'your-bot-token',
    'db_host'   => 'your.db.host',
    'db_user'   => 'username', 
    'db_pass'   => 'password',
    'db_name'   => 'dbname',
    'db_prefix' => 'vpn_',
    
    'ssh_user'              => 'root',
    'ssh_host'              => 'your-vpn-host',
    'ssh_fingerprint'       => 'ssh-host-public-key',
    'ssh_public_key_file'   => '/where/is/your/id_rsa.pub',
    'ssh_private_key_file'  => '/where/is/your/id_rsa',
    
    'ipsec_secrets'         => '/etc/ipsec.secrets',  // this is the default
    'ipsec_command'         => 'ipsec secrets',       // this is the default
    
    ########################################################################################
    ###-NEVER USE IT IN PRODUCTION. It's good idea to exclude this from production       ###
    ###                                                                                  ###
    'drop_tables_on_startup' => 'off',  // use 'YES I UNDERSTAND WHAT I DO!'             ###
                                        // to drop all bot tables and start from scratch ###
    "I'm sure"               => false,  // also you have set this to true                ###
    ########################################################################################

];

date_default_timezone_set('Europe/Moscow');

/**
 * NEVER EDIT THE LINES BELOW
 * DO NOT INCLUDE THEM IN YOUR config.php
 */

$config_defaults = $config;
include_once 'config.php';

$config = array_merge($config_defaults, $config);

require_once 'db.php';
require_once 'handlers.php';
require_once 'trackers.php';
require_once 'inc/global.php';
require_once 'inc/lang_ru.php';
