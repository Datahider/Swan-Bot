<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerCommandC
 *
 * @author drweb
 */
class HandlerCommandLogin extends \losthost\telle\Handler {
    
    private $connection;
    
    protected function init(): void {
        $this->connection = null;
    }

    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {
        $message = $update->getMessage();
        if (!$message) {
            return false;
        }
        $matches = [];
        $result = preg_match("/^\/(.+)$/", $message->getText(), $matches);
        
        if ($result) {
            try { 
                $this->connection = new DBConnection(Context::$user, $matches[1]);
            } catch (Exception $exc) {
                // TODO - анализировать исключение -10002 - Not Found
                return false;
            }
        } else {
            $this->connection = null;
        }
        
        return $result;
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {

        
        $message = losthost\telle\Bot::$api->sendMessage(
                    Context::$user->id,
                    $this->connection->asString(), 
                    "HTML", false, null, 
                    HandlerCallbackConnectionEdit::kbdMain($this->connection->id));
        
        Context::$session->set(DBSession::FIELD_PRIORITY_HANDLER, HandlerCallbackConnectionEdit::class);
        Context::$session->set(DBSession::FIELD_DATA, [
            'message_id' => $message->getMessageId(),
            'connection_id' => $this->connection->id]);
        
        return true;
    }
    
    public function isFinal(): bool {
        return false;
    }
}
