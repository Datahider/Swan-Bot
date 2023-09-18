<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerExtended
 *
 * @author drweb
 */
abstract class HandlerExtended extends \losthost\telle\Handler {
    
    static protected function setPriority(mixed $data, string $handler=null) {
        if ($handler === null) {
            $handler = static::class;
        } else {
            error_log('Passing second parameter is not recomended in '. __FILE__. ':'. __LINE__);
        }
        
        Context::$session->set(DBSession::FIELD_PRIORITY_HANDLER, $handler);
        Context::$session->set('data', $data);
    }
    
    static protected function unsetPriority() {
        Context::$session->set(DBSession::FIELD_PRIORITY_HANDLER, null);
        Context::$session->set('data', null);
    }
}
