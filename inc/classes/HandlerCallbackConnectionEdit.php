<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerCallbackConnectionEdit
 *
 * @author drweb
 */
class HandlerCallbackConnectionEdit extends HandlerExtended {
    
    protected $command;
    protected $connection;
    protected $message_id;

    protected function init(): void {
        $this->connection = null;
        $this->command = null;
        $this->message_id = null;
    }

    public function isFinal(): bool {
        return false;
    }
    
    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {

        return false
            || $this->checkCallback($update)
            || $this->checkPriorityUpdate($update);
        
    }

    protected function checkCallback(\TelegramBot\Api\Types\Update $update) {

        $callback = $update->getCallbackQuery();
        
        if (!$callback) {
            return false;
        }
        
        $data = $callback->getData();
        
        $m = [];

        return false 
                || preg_match("/^(prolong)_(\d+)/", $data, $m) 
                        && $this->setValues($m[1], $m[2], $callback->getMessage()->getMessageId()) 
                || preg_match("/^(delete)_(\d+)/", $data, $m)   
                        && $this->setValues($m[1], $m[2], $callback->getMessage()->getMessageId()) 
                || preg_match("/^(confirm_delete)_(\d+)/", $data, $m)   
                        && $this->setValues($m[1], $m[2], $callback->getMessage()->getMessageId()) 
                || preg_match("/^(cancel_delete)_(\d+)/", $data, $m)   
                        && $this->setValues($m[1], $m[2], $callback->getMessage()->getMessageId()) 
                || preg_match("/^(chpass)_(\d+)/", $data, $m)
                        && $this->setValues($m[1], $m[2], $callback->getMessage()->getMessageId()) 
                || preg_match("/^(cancel)_(\d+)/", $data, $m)         
                        && $this->setValues($m[1], $m[2], $callback->getMessage()->getMessageId()); 
    }
    
    protected function setValues($command, $connection_id, $message_id) {
        $this->command = $command;
        $this->message_id = $message_id;
        try { // пробуем считать соединение
            $this->connection = new DBConnection(Context::$session->user, (int)$connection_id);
        } catch (Exception $exc) {
            // TODO - анализировать исключение
            // Не наше или удалено. Подчистим за собой 
            $this->unsetPriority();
            return false;
        }
        return true;
    }
    
    protected function checkPriorityUpdate(\TelegramBot\Api\Types\Update &$update) {
        if (Context::$session->priority_handler == self::class) {
            $this->command = Context::$session->command;
            $this->message_id = Context::$session->data['message_id'];
            try { // пробуем считать соединение
                $this->connection = new DBConnection(Context::$session->user, Context::$session->data['connection_id']);
            } catch (Exception $exc) {
                // TODO - анализировать исключение
                // Не наше или удалено. Подчистим за собой 
                $this->unsetPriority();
                return false;
            }

            $message = $update->getMessage();
            if ($message && $message->getText()) {
                $this->command = 'rename';
            }
            
            return true;
        }
    }
    
    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {

        return false
            || $this->command == 'prolong'          && $this->handleProlong($update)
            || $this->command == 'chpass'           && $this->handleChpass($update)
            || $this->command == 'delete'           && $this->handleDelete($update)
            || $this->command == 'rename'           && $this->handleRename($update)
            || $this->command == 'confirm_delete'   && $this->handleConfirmDelete($update)
            || $this->command == 'cancel_delete'    && $this->handleCancelDelete($update)
            || $this->command == 'cancel'           && $this->handleCancel($update)
            ;
    }

    protected function answerCallback(TelegramBot\Api\Types\Update &$update) {
        try {
            \losthost\telle\Bot::$api->answerCallbackQuery($update->getCallbackQuery()->getId());
        } catch (Exception $exc) {
            // Nothing to do
        }
    }
    
    protected function handleProlong(TelegramBot\Api\Types\Update &$update) {
        $this->removeKeyboard($this->message_id);
        HandlerPromoCode::askPromoCode($this->connection);
        $this->answerCallback($update);
        return true;
    }
    
    protected function handleChpass(TelegramBot\Api\Types\Update &$update) {
        $this->connection->password = DBConnection::genPassword();
        $this->setPriority([
            'connection_id' => $this->connection->id,
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
            'keyboard' => $this->kbdMain($this->connection->id)
        ]);
        $this->connection->write();
        $this->answerCallback($update);
        return true;
    }

    protected function handleRename(\TelegramBot\Api\Types\Update &$update) {
        $new_name = $update->getMessage()->getText();
        $result = false;
        
        if (!preg_match("/^\//", $new_name)) {
            $this->connection->description = $new_name;
            $this->connection->write();
            $result = true;
        }

        $this->removeKeyboard();
        $this->unsetPriority();
        return $result;
    }
    
    protected function handleDelete($update) {
        $this->setKeyboardDelete();
        $this->setPriority([
            'connection_id' => $this->connection->id,
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
        ]);
        $this->answerCallback($update);
        return true;
    }
    
    protected function handleCancelDelete(TelegramBot\Api\Types\Update &$update) {
        $this->setKeyboardMain();
        $this->setPriority([
            'connection_id' => $this->connection->id,
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
        ]);
        $this->answerCallback($update);
        return true;
    }
    
    protected function handleConfirmDelete($update) {
        $this->setPriority([
            'connection_id' => $this->connection->id,
            'message_id' => $update->getCallbackQuery()->getMessage()->getMessageId(),
        ]);
        $this->connection->delete();
        $this->unsetPriority();
        return true;
    }
    
    protected function handleCancel($update) {
        $this->removeKeyboard($update);
        $this->unsetPriority();
        $this->answerCallback($update);
        return true;
    }
    
    protected function removeKeyboard() {
        try {
            losthost\telle\Bot::$api->editMessageReplyMarkup(
                Context::$user->id, 
                $this->message_id,
                null);
        } catch (\Exception $e) {
            // TODO - проверять код и текст исключения 400 
            // Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message
        }
    }
    
    protected function setKeyboardDelete() {
        try {
            losthost\telle\Bot::$api->editMessageReplyMarkup(
                Context::$user->id, 
                $this->message_id,
                $this->kbdConfirmDelete($this->connection->id));
        } catch (\Exception $e) {
            // TODO - проверять код и текст исключения 400 
            // Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message
        }
    }
    
    protected function setKeyboardMain() {
        try {
            losthost\telle\Bot::$api->editMessageReplyMarkup(
                Context::$user->id, 
                $this->message_id,
                $this->kbdMain($this->connection->id));
        } catch (\Exception $e) {
            // TODO - проверять код и текст исключения 400 
            // Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message
        }
    }

    static function showConnection(DBConnection $connection) {
        $chat_id = Context::$user->id;
        $text = $this->connection->asString();
        $keyboard = self::kbdMain($connection->id);
        
        $message = \losthost\telle\Bot::$api->sendMessage($chat_id, $text, 'HTML', false, null, $keyboard);
        
        self::setPriority(['connection_id', $connection->id, 'message_id' => $message->getMessageId()]);
    }
    
    static function kbdMain(int $connection_id) {
        return new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
            [
                [ 'text' => __('Продлить'), 'callback_data' => 'prolong_'. $connection_id], 
                [ 'text' => __('Удалить'), 'callback_data' => 'delete_'. $connection_id]
            ],
            [
                [ 'text' => __('Сменить пароль'), 'callback_data' => 'chpass_'. $connection_id],
                [ 'text' => __('Отмена'), 'callback_data' => 'cancel_'. $connection_id],
            ]
        ]);
    }

    static function kbdConfirmDelete(int $connection_id) {
        return new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
            [
                [ 'text' => __('Подтвердить удаление'), 'callback_data' => 'confirm_delete_'. $connection_id], 
                [ 'text' => __('Отменить удаление'), 'callback_data' => 'cancel_delete_'. $connection_id]
            ],
        ]);
    }
}
