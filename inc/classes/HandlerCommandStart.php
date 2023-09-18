<?php

class HandlerCommandStart extends losthost\telle\Handler {

    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {
        $message = $update->getMessage();
        if (!$message) {
            return false;
        }
        return false
            || preg_match("/^\/[Ss][Tt][Aa][Rr][Tt](\s.*)*$/", $message->getText())
            || preg_match("/^\/[Ll][Ii][Ss][Tt](\s.*)*$/", $message->getText());
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {
        if (!Context::$answered) {
            $this->showConnections();
        } 
        return true;
    }
    
    protected function showConnections() {
        \losthost\telle\Bot::$api->sendMessage(
                Context::$user->id, 
                __("Ваши подключения:", [
                    'connections' => (new ViewConnections())->show()
                ]),
                "HTML"
                   
        );
    }

    protected function init(): void {
        
    }

    public function isFinal(): bool {
        return false;
    }
}
