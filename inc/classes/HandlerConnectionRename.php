<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerConnectionRename
 *
 * @author drweb
 */
class HandlerConnectionRename extends HandlerExtended {
    
    protected $connection; 
    
    protected function check(\TelegramBot\Api\Types\Update &$update): bool {
        if (Context::$session->priority_handler == self::class) {
            $this->connection = new DBConnection(
                    Context::$user, Context::$session->data['connection_id']);
            return true;
        }
        return false;
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update): bool {
        $message =  $update->getMessage();
        if (!$message) {
            $this->unsetPriority();
            return false;
        }
        
        $text = $message->getText();
        if (!$text || substr($text, 0, 1) == '/') {
            $this->unsetPriority();
            return false;
        }
        
        $this->connection->description = $text;
        $this->connection->write();
        $this->unsetPriority();
        return true;
    }

    protected function init(): void {
        $this->connection = null;
    }

    public function isFinal(): bool {
        return false;
    }
    
    static function askNewName(DBConnection $connection) {
        
        $message = \losthost\telle\Bot::$api->sendMessage(
                Context::$user->id, 
                $connection->asString('RenamePrompt'), 
                'HTML');
        self::setPriority([ 
            'connection_id' => $connection->id]);
    }
    
}
