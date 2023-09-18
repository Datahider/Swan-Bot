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
    
        $this->user_id = $event->object->user;
        
        switch ($event->type) {
            case losthost\DB\DBEvent::AFTER_INSERT:
                $this->showToUser($event->object->asString(DBObjectExtended::FMT_NEW));
                break;
            case losthost\DB\DBEvent::AFTER_UPDATE:
                $this->showToUser($event->object->asString(DBObjectExtended::FMT_DEFAULT));
                $this->updateSecrets();
                break;
            case losthost\DB\DBEvent::INTRAN_DELETE:
                $this->showToUser($event->object->asString(DBObjectExtended::FMT_DELETED));
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

    protected function showToUser($text) {
        if (isset(Context::$session) && Context::$session->priority_handler == HandlerCallbackConnectionEdit::class) {
            try {
                losthost\telle\Bot::$api->editMessageText(
                    Context::$user->id, 
                    Context::$session->data['message_id'], 
                    $text,
                    "HTML", false,
                    isset(Context::$session->data['keyboard']) ? Context::$session->data['keyboard'] : null);
            } catch (\Exception $e) {
                if ($e->getCode() == 400) {
                    error_log($e->getMessage());
                } else {
                    throw $e; // TODO - возможно стоить игнорировать или повторять при ошибках соединения
                }
            }
        } else {
            losthost\telle\Bot::$api->sendMessage(
                $this->user_id, 
                $text,
                "HTML");
        }
    }
    
}
