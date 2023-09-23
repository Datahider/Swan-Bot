<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerCommandPromo
 *
 * @author drweb
 */
class HandlerCommandPromo extends HandlerExtended {
    //put your code here
    protected function check(\TelegramBot\Api\Types\Update &$update): bool {
        $message = $update->getMessage();
        if (!$message) {
            return false;
        }

        $result = preg_match("/^\/[Pp][Rr][Oo][Mm][Oo](\s.*)*$/", $message->getText());
        return $result;
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update): bool {
        \losthost\telle\Bot::$api->sendMessage(
                Context::$user->id, __(self::class. '_Сообщение'), 
                "HTML", true, null,
                new TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
                    [[ 
                        'text' => __(self::class. '_Кнопка_поделиться'), 
                        'switch_inline_query_chosen_chat' => [
                            'allow_user_chats' => true,
                            'allow_group_chats' => true,
                            'allow_channel_chats' => true,
                        ]
                    ]]
                ]));
        return true;
    }

    protected function init(): void {
        
    }

    public function isFinal(): bool {
        return false;
    }
}
