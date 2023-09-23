<?php

$lang['ru'] = [
    'date_format' => 'd-m-Y H:i:s',
    'Продлить' => '🚀 Продлить',
    'Переименовать' => '📝 Переименовать',
    'Сменить пароль' => '🔐 Новый пароль',
    'Удалить' => '❌ Удалить',
    'Отмена' => '✔️ Готово',
    'Подтвердить удаление' => '❌ Подтвердить удаление',
    'Отменить удаление' => '🖐 Отменить удаление',
    'Приветствие нового пользователя' => <<<END
            Привет. Через этого бота вы можете управлять своими VPN подключениями.
            END,
    'Инструкции' =>
        <<<END
            <u><b>Инструкции по настройке:</b></u> 
    
            🔸 <b><a href="https://todd-vpn.ru/kak-nastroit/android/">Android</a></b>
            🔸 <b><a href="https://pio.su">iOS</a></b>
            🔸 <b><a href="https://pio.su">MacOS</a></b>
            🔸 <b><a href="https://todd-vpn.ru/kak-nastroit/windows/">Windows</a></b>
    
            Также инструкции можно получить в <a href="https://t.me/toddVPN">группе поддержки</a> в Телеграм
    
            <u><b>Команды бота:</b></u>
    
            /list  - отображение списка подключений.
            /new   - создание нового подключениия.
            /promo - получение кодов активации.
            /help  - вывод этого сообщения.
            END,
    'DBConnection::stringNew' =>
        <<<END
            Создано новое подключение: <b>%description%</b> /%login%

            Имя пользователя: <b>%login%</b>
            Пароль: <b><span class="tg-spoiler">%password%</span></b>
            Активно до: <b>%active_till%</b>

            Для просмотра инструкций по настройке введите /help
            END, 
    'DBConnection::stringRenamePrompt' => 
        <<<END
            Отправьте новое название для <b>%description%</b> /%login%
            END,
    'Ваши подключения:' =>
        <<<END
            <b><u>Ваши подключения:</u></b>
    
            %connections%
    
            Для просмотра данных и редактирования подключения нажмите на имя пользователя
            END,
    'DBConnection::stringDefaultDescription' => 'Новое подключение',
    'DBConnection::stringDefault' =>
        <<<END
            🔌 <b>%description%</b> /%login%
    
            👨 Имя пользователя: <b>%login%</b>
            🗝 Пароль: <b><span class="tg-spoiler">%password%</span></b>
            ⏱ Активно до: <b>%active_till%</b>

            Для просмотра инструкций по настройке введите /help
            END,
    'DBConnection::stringNewPass' => 
        <<<END
            🔌 <b>%description%</b> /%login%
    
            👨 Имя пользователя: <b>%login%</b>
            🗝 Новый пароль: <b><span class="tg-spoiler">%password%</span></b>
            ⏱ Активно до: <b>%active_till%</b>

            Для просмотра инструкций по настройке введите /help
            END,
    'DBConnection::stringDeleted' =>
        <<<END
            ❌  <b>%description%</b> /%login%
    
            Подключение удалено.
    
            Для создания нового подключения введите /new.
            /list - просмотр списка подключений.
            END,
    'DBConnection::stringPromoPrompt' =>
        <<<END
            Для продления подключения <b>%description%</b> /%login% отправьте промокод.
    
            Чтобы узнать как и где можно получить промокоды нажмите /promo
            END,
    'DBConnection::stringNewDescription' =>
        <<<END
            Название подключения изменено на <b>%description%</b> /%login%
            END,
    'DBConnection::stringNewEndDate' =>
        <<<END
            Подключение <b>%description%</b> /%login% продлено до <b>%active_till%</b>.
            END,
    'Не возможно создать новое сединение' => 
        <<<END
            Вы можете бесплатно создать только одно соединение.
            END,
    'HandlerCommandPromo_Сообщение' => <<<END
            Промокоды для продления подключения можно купить или получить бесплатно <a href="https://t.me/toddVPN/46/47">тут</a>.
    
            Также промокод можно получить поделившись информацией об этом боте с друзьями. 
                
            За каждое отправленное другу сообщение вы можете получить до 7 дней бесплатного доступа.
            <a href="https://todd-vpn.ru/promo-rules/">Простые правила промо-акции</a> 
            END,
    'HandlerCommandPromo_Кнопка_поделиться' => 'Поделиться',
    'HandlerInlineQuery_Изображение_результата_0' => 'https://todd-vpn.ru/wp-content/uploads/2023/09/cropped-vpn-e1695075481845.png',
    'HandlerInlineQuery_Кнопка_результата_0' => 'Получить VPN',
    
    'HandlerInlineQuery_Изображение_результата_1' => 'https://todd-vpn.ru/wp-content/uploads/2023/09/cropped-vpn-e1695075481845.png',
    'HandlerInlineQuery_Кнопка_результата_1' => 'Получить VPN',
];
