<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of TrackerConnection
 *
 * @author drweb
 */
class TrackerConnection extends losthost\DB\DBTracker {
     
    protected $user_id;

    public function track(\losthost\DB\DBEvent $event) {
        
        if ($event->comment == 'NEW_LOGIN') {
            $this->updateSecrets();
            return;                         }

        if (preg_match("/NEW_PASS|DELETED/", $event->comment)) {
            $message_to_edit = $event->data;
        } else {
            $message_to_edit = null;
        }
    
        switch ($event->type) {
            case losthost\DB\DBEvent::AFTER_INSERT:
                $this->user_id = $event->object->user;
                $this->showToUser($event->object->asString(DBObjectExtended::FMT_NEW), $event->object);
                break;
            case losthost\DB\DBEvent::AFTER_UPDATE:
                $this->user_id = $event->object->user;
                if (array_search('password', $event->fields) !== false) {
                    $this->showToUser($event->object->asString('NewPass'), $event->object, true, $message_to_edit);
                } elseif (array_search('description', $event->fields) !== false) {
                    $this->showToUser($event->object->asString('NewDescription'), $event->object, false);
                } elseif (array_search('active_till', $event->fields) !== false) {
                    $this->showToUser($event->object->asString('NewEndDate'), $event->object, false);
                } else {
                    $this->showToUser($event->object->asString(DBObjectExtended::FMT_DEFAULT), $event->object, true);
                }
                $this->updateSecrets();
                break;
            case losthost\DB\DBEvent::INTRAN_DELETE:
                $this->user_id = $event->object->user;
                $this->showToUser($event->object->asString(DBObjectExtended::FMT_DELETED), $event->object, false, $message_to_edit);
                break;
            case losthost\DB\DBEvent::AFTER_DELETE:
                $this->updateSecrets();
                break;
        }
    }
    
    public function updateSecrets() {
        global $config;
        
        $lock = new DBLock("updateSecrets", 20);
        
        $vipw = new SecretsUpdate(
                $config['ssh_user'], 
                $config['ssh_host'], 
                $config['ssh_fingerprint'], 
                $config['ssh_public_key_file'], 
                $config['ssh_private_key_file']);
        
        $vipw->update();

        $lock->delete();
    }

    static function showToUser($text, $connection, $show_keyboard=false, $message_id=null) {
        if ($show_keyboard) {
            $keyboard = HandlerCallbackConnectionEdit::kbdMain($connection->id);
        } else {
            $keyboard = null;
        }
        if ($message_id !== null) {
            try {
                losthost\telle\Bot::$api->editMessageText(
                    $connection->user, 
                    $message_id, 
                    $text,
                    "HTML", false,
                    $keyboard);
            } catch (\Exception $e) {
                if ($e->getCode() == 400) {
                    error_log($e->getMessage());
                } else {
                    throw $e; // TODO - возможно стоить игнорировать или повторять при ошибках соединения
                }
            }
        } else {
            losthost\telle\Bot::$api->sendMessage(
                $connection->user, 
                $text,
                "HTML", false, null, 
                $keyboard);
        }
    }
    
}
