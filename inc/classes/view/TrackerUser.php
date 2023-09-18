<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of UserTracker
 *
 * @author drweb
 */
class TrackerUser extends losthost\DB\DBTracker {
    //put your code here
    public function track(\losthost\DB\DBEvent $event) {
        switch ($event->type) {
            case losthost\DB\DBEvent::INTRAN_INSERT:
                \losthost\telle\Bot::$api->sendMessage(
                    $event->object->id,
                    __("Приветствие нового пользователя"),
                );
                $this->connectionCreate($event->object->id);
                Context::$answered = true;
                break;
        }
    }
    
    protected function connectionCreate(int $for_user) {
        new DBConnection($for_user);
    }
}
