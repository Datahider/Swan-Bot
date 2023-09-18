<?php

/**
 * Description of Context
 *
 * @author drweb
 */
class Context extends losthost\telle\Handler {
    
    public static $user;
    public static $chat;
    public static $message_tread_id;
    public static $session;
    public static $language_code;
    
    public static $message_thread_id;
    public static $answered;
    
    protected function check(\TelegramBot\Api\Types\Update &$update) : bool {
        return true;
    }

    protected function init(): void {
        self::$answered = false;
    }

    public function isFinal(): bool {
        return false;
    }
    
    protected function initLast() {
        self::$language_code = self::$user->language_code;
        self::$session = new DBSession(self::$user, self::$chat, self::$message_thread_id);
    }


    protected function handle(\TelegramBot\Api\Types\Update &$update) : bool {
        
        if ($callback_query = $update->getCallbackQuery()) {
            $this->initByCallbackQuery($callback_query);
        } elseif ($chanel_post = $update->getChannelPost()) {
            $this->initByChannelPost($chanel_post);
        } elseif ($chosen_inline_result = $update->getChosenInlineResult()) {
            $this->initByChosenInlineResult($chosen_inline_result);
        } elseif ($edited_channel_post = $update->getEditedChannelPost()) {
            $this->initByEditedChannelPost($edited_channel_post);
        } elseif ($edited_message = $update->getEditedMessage()) {
            $this->initByEditedMessage($edited_message);
        } elseif ($inline_query = $update->getInlineQuery()) {
            $this->initByInlineQuery($inline_query);
        } elseif ($message = $update->getMessage()) {
            $this->initByMessage($message);
        } elseif ($poll = $update->getPoll()) {
            $this->initByPoll($poll);
        } elseif ($poll_answer = $update->getPollAnswer()) {
            $this->initByPollAnswer($poll_answer);
        } elseif ($pre_checkout_query = $update->getPreCheckoutQuery()) {
            $this->initByPreCheckoutQuery($pre_checkout_query);
        } elseif ($shipping_query = $update->getShippingQuery()) {
            $this->initByShippingQuery($shipping_query);
        } else {
            throw new \Exception("Can't init context.");
        }
        
        $this->initLast();
        
        return false;
    }
    
    protected function initByCallbackQuery(TelegramBot\Api\Types\CallbackQuery &$callback_query) {
        $from = $callback_query->getFrom();
        Context::$language_code = $from->getLanguageCode();
        self::$user = new DBUser($from);
    }
    
    protected function initByChannelPost(TelegramBot\Api\Types\Message &$channel_post) {
        $this->initByMessage($channel_post);
    }
    
    protected function initByChosenInlineResult(TelegramBot\Api\Types\Inline\ChosenInlineResult &$chosen_inline_result) {
        
    }
    
    protected function initByEditedChannelPost(TelegramBot\Api\Types\Message &$edited_channel_post) {
        $this->initByMessage($edited_channel_post);
    }
    
    protected function initByEditedMessage(TelegramBot\Api\Types\Message &$edited_message) {
        $this->initByMessage($edited_message);
    }
    
    protected function initByInlineQuery(\TelegramBot\Api\Types\Inline\InlineQuery &$inline_query) {
        
    }
    
    protected function initByMessage(TelegramBot\Api\Types\Message &$message) {
        $from = $message->getFrom();
        Context::$language_code = $from->getLanguageCode();
        self::$user = new DBUser($from);
    }
    
    protected function initByPoll(\TelegramBot\Api\Types\Poll &$poll) {
        
    }
    
    protected function initByPollAnswer(TelegramBot\Api\Types\PollAnswer &$poll_answer) {
        
    }
    
    protected function initByPreCheckoutQuery(TelegramBot\Api\Types\Payments\Query\PreCheckoutQuery &$pre_checkout_query) {
        
    }
    
    protected function initByShippingQuery(\TelegramBot\Api\Types\Payments\Query\ShippingQuery &$shipping_query) {
        
    }

}
