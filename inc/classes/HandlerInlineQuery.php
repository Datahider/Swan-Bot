<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of HandlerInlineQuery
 *
 * @author drweb
 */
class HandlerInlineQuery extends HandlerExtended {
    
    protected function check(\TelegramBot\Api\Types\Update &$update): bool {
        $inline_query = $update->getInlineQuery();
        return (bool)$inline_query;
    }

    protected function handle(\TelegramBot\Api\Types\Update &$update): bool {
        $inline_query = $update->getInlineQuery();
        
        \losthost\telle\Bot::$api->answerInlineQuery($inline_query->getId(), $this->getResults($inline_query));
        return true;
    }
    
    protected function getResults(TelegramBot\Api\Types\Inline\InlineQuery $inline_query) {
        
        $results[0] = new TelegramBot\Api\Types\Inline\QueryResult\Article(
                0, 
                __(self::class. '_Название_результата_0'), 
                __(self::class. '_Описание_результата_0'), 
                __(self::class. '_Изображение_результата_0'), 
                null, null,
                [
                'message_text' => __(self::class. '_Текст_результата_0'),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true],
                new TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
                    [[ 
                        'text' => __(self::class. '_Кнопка_результата_0'),
                        'callback_data' => 'promo_'. Context::$user->id 
                    ]]
                ]));
        
        $results[1] = new TelegramBot\Api\Types\Inline\QueryResult\Article(
                1, 
                __(self::class. '_Название_результата_1'), 
                __(self::class. '_Описание_результата_1'), 
                __(self::class. '_Изображение_результата_1'), 
                null, null,
                [
                'message_text' => __(self::class. '_Текст_результата_1'),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true],
                new TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
                    [[ 
                        'text' => __(self::class. '_Кнопка_результата_1'),
                        'callback_data' => 'promo_'. Context::$user->id 
                    ]]
                ]));
        
        return $results;
    }

    protected function init(): void {
        
    }

    public function isFinal(): bool {
        return false;
    }
}
