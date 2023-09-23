<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of DBConnections
 *
 * @author drweb
 */
class DBConnection extends DBObjectExtended {

    const TABLE_NAME = 'connections';
    
    const SQL_CREATE_TABLE = <<<END
            CREATE TABLE IF NOT EXISTS %TABLE_NAME% (
                id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                user bigint UNSIGNED NOT NULL,
                password varchar(20) NOT NULL,
                is_active tinyint NOT NULL,
                description varchar(1024) NOT NULL,
                PRIMARY KEY (id)
            ) COMMENT = 'v1.0.0'
            END;
    
    const SQL_UPGRADE_FROM_1_0_0 = <<<END
            ALTER TABLE %TABLE_NAME% COMMENT = 'v1.1.0',
            ADD COLUMN active_till DATETIME;
            END;

    const SQL_UPGRADE_FROM_1_1_0 = <<<END
            ALTER TABLE %TABLE_NAME% COMMENT = 'v1.1.1',
            ADD COLUMN prefix varchar(10) NOT NULL DEFAULT 'u',
            ADD COLUMN login varchar(30)
            END;

    public function __construct(int|DBUser $user, string | int | null $login=null) {
        if (is_a($user, DBUser::class)) {
            $user = $user->id;
        }
        
        if ($login === null) {
            $grace_period = (new losthost\telle\BotParam('new_connection_grace_days', 7))->value;
            $grace_interval = date_interval_create_from_date_string("+$grace_period days");
            
            parent::__construct();
            $this->user = $user;
            $this->password = self::genPassword();
            $this->is_active = 1;
            $this->active_till = date_create_immutable()->add($grace_interval);
            $this->description = $this->asString("DefaultDescription");
            $this->write();
        } else {
            if (is_string($login)) {
                parent::__construct('user = ? AND login = ?', [$user, $login]);
            } else {
                parent::__construct('user = ? AND id = ?', [$user, $login]);
            }
        }
    }
    
    protected function userConnectionsCount() {
        
        $sth = $this->prepare(<<<END
            SELECT COUNT(id) 
            FROM %TABLE_NAME%
            WHERE user = ?
            END);
        
        $sth->execute([$this->user]);
        
        return $sth->fetchColumn();
        
    }
    
    public function canProlongGrace() {
        
        $happy_seconds = 3600 * \losthost\telle\Bot::param('happy_hours', 3);
        
        return true
            && $this->userConnectionsCount() == 1 
            && $this->active_till->getTimestamp() - $happy_seconds <= time();
    }
    
    protected function prolong($days) {
        $this->active_till = $this->active_till->add(date_interval_create_from_date_string("+$days days"));
        $this->write();
    }

    
    static public function genPassword($len=14) {

        $result = '';

        for ($index = 0; $index < $len; $index++) {
            $result .= substr(
                    '_123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ', 
                    random_int(0, 58), 
                    1);
        }

        return $result;
    }

    protected function beforeInsert($comment, $data) {
        if (!$this->prefix) {
            $this->prefix = (new losthost\telle\BotParam('user_prefix', 'u'))->value;
        }
        parent::beforeInsert($comment, $data);
    }
    
    protected function afterInsert($comment, $data) {
        $this->__immutable = false;
        $this->login = $this->prefix. $this->id;
        parent::afterInsert($comment, $data);
        $this->write("NEW_LOGIN"); // Повторная запись внутри события создания не должна вызывать вывод чего либо
    }
    
    public function __get($name) {
        $value = parent::__get($name);
        
        if ($name == 'active_till') {
            $result = new DateTimeImmutable($value);
            return $result;
        }
        return $value;
    }
    
    public function __set($name, $value) {
        if ($name == 'active_till' && (is_a($value, DateTimeImmutable::class) || is_a($value, DateTime::class))) {
            $value = $value->format(\losthost\DB\DB::DATE_FORMAT);
        }
        parent::__set($name, $value);
    }
    
    public function asArray($date_format=null, $false=null, $true=null) {
        
        if ($date_format === null)
            { $date_format = losthost\DB\DB::DATE_FORMAT; }
        
        if ($false === null)
            { $false = 'false'; }

        if ($true === null)
            { $true = 'true'; }
        
        $array = parent::asArray();
        
        $active_till = new DateTimeImmutable($array['active_till']);
        $array['active_till'] = $active_till->format($date_format);
        $array['is_active'] = $array['is_active'] ? $true : $false;
        
        return $array;
    }
}
