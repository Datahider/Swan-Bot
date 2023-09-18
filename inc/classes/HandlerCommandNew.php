<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandrerCommandNew
 *
 * @author drweb
 */
class HandlerCommandNew extends \losthost\telle\Handler {
    
    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {
        $message = $update->getMessage();
        if (!$message) {
            return false;
        }

        $result = preg_match("/^\/[Nn][Ee][Ww](\s.*)*$/", $message->getText());
        return $result;
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {
        
        if ($this->canCreateConnection()) {
            new DBConnection(Context::$user);
        } else {
            losthost\telle\Bot::$api->sendMessage(
                Context::$user->id,
                __('Не возможно создать новое сединение'),
                "HTML"
            );
        }
        
        return true;
    }
    
    protected function canCreateConnection() {
        return true;
    }

    protected function init(): void {
        
    }

    public function isFinal(): bool {
        return false;
    }
}
