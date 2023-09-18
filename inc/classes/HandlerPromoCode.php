<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerPromoCode
 *
 * @author drweb
 */
class HandlerPromoCode extends HandlerExtended {
    
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
        
        try {
            $promo_code = new DBPromoCode($text);
            $promo_code->activate($this->connection);
        } catch (Exception $exc) {
            $this->notifyFailed();
        }
        $this->removeKeyboard();
        $this->unsetPriority();
        return true;
    }

    protected function notifyFailed() {
        losthost\telle\Bot::$api->sendMessage(
                Context::$user->id,
                __("Ошибка активации промокода"), 
                "HTML");
    }
    
    protected function init(): void {
        $this->connection = null;
    }

    public function isFinal(): bool {
        return false;
    }
    
    static function askPromoCode(DBConnection $connection) {
        
        $keyboard = $connection->canProlongGrace() ? self::kbdProlong4Free($connection->id) : null;
        $message = \losthost\telle\Bot::$api->sendMessage(
                Context::$user->id, 
                $connection->asString('PromoPrompt'), 
                'HTML', false, null,
                $keyboard);
        self::setPriority([ 
            'connection_id' => $connection->id,
            'message_id' => $message->getMessageId()]);
    }

    protected function removeKeyboard() {
        try {
            losthost\telle\Bot::$api->editMessageReplyMarkup(
                Context::$user->id, 
                Context::$session->data['message_id'],
                null);
        } catch (\Exception $e) {
            // TODO - проверять код и текст исключения 400 
            // Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message
        }
    }
    
    static function kbdProlong4Free($connection_id) {
        return new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
            [[ 'text' => 'Продлить на сутки бесплатно', 'callback_data' => '1day4free_'. $connection_id]]]);
        
    }
}
