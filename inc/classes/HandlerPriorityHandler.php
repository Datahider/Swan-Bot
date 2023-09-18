<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerPriorityHandler
 *
 * @author drweb
 */
class HandlerPriorityHandler extends \losthost\telle\Handler {
    
    protected $handler;

    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {
    
        $this->handler = null;
        $priority_handler = Context::$session->priority_handler;
        if (!$priority_handler) {
            return false;
        }
        
        $this->handler = new $priority_handler();
        if (!$this->handler->checkUpdate($update)) {
            Context::$session->set('priority_handler', null);
            return false;
        }
        
        return true;
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {
        return $this->handler->handleUpdate($update);
    }

    protected function init(): void {
        
    }

    public function isFinal(): bool {
        return false;
    }
}
