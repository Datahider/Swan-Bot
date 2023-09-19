<?php

class HandlerCommandHelp extends losthost\telle\Handler {
    
    protected function init(): void {
    }

    public function isFinal(): bool {
        return false;
    }

    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {
        return true;
    }
    
    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {
        \losthost\telle\Bot::$api->sendMessage(
                Context::$user->id, 
                __("Инструкции"), "HTML", true);
        return true;
    }
}
