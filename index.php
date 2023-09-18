<?php
define('DEBUG_WORKER', false);


require_once 'vendor/autoload.php';
require_once 'etc/config-defaults.php';

if (defined('DEBUG_WORKER') && DEBUG_WORKER) {
    $worker = new \losthost\telle\Worker(1);
    $worker->run();    
} else {
    losthost\telle\Bot::init();
    
    ##############################################################################
    #-BE AWARE !!! DELETE THEESE LINES JUST FOR A CASE IN PRODUCTION ENVIRONMENT-#
    if ( $config['drop_tables_on_startup'] === 'YES I UNDERSTAND WHAT I DO!') {  #
        losthost\DB\DB::dropAllTables(true, $config["I'm sure"]);                #
    }                                                                            #
    ##############################################################################
    
    \losthost\telle\PendingUpdate::initDataStructure();
    DBChat::initDataStructure();
    DBConnection::initDataStructure();
    DBLock::initDataStructure();
    DBPromoCode::initDataStructure();
    DBSession::initDataStructure();
    DBUser::initDataStructure();

    if (isset($argv[1]) && $argv[1] == 'truncate-pending') {
        $drop = new losthost\telle\BotParam('truncate_updates_on_startup');
        $drop->value = 1;
    }
    
    \losthost\telle\Bot::run();
}

