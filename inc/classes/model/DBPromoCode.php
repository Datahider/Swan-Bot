<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of DBPromoCode
 *
 * @author drweb
 */
class DBPromoCode extends DBObjectExtended {
    
    const TABLE_NAME = 'promo_codes';
    
    const SQL_CREATE_TABLE = <<<END
            CREATE TABLE IF NOT EXISTS %TABLE_NAME% (
                code varchar(30) NOT NULL,
                valid_till datetime NOT NULL,
                period_days int UNSIGNED NOT NULL,
                initial_count int UNSIGNED NOT NULL,
                remain_count int UNSIGNED NOT NULL,
                PRIMARY KEY (code)
            ) COMMENT = 'v1.0.0'
            END;
    
    const SQL_ACTIVATE = <<<END
            UPDATE %TABLE_NAME% 
            SET remain_count = remain_count - 1 
            WHERE code = ?
            END;
    
    public function __construct($code) {
        parent::__construct('code = ?', $code);
    }
    
    public function activate(DBConnection &$connection) {
        $sth = $this->prepare(self::SQL_ACTIVATE);
        
        try {
            losthost\DB\DB::$pdo->beginTransaction();
            $sth->execute([$this->code]);
            $connection->active_till = $connection->active_till->add(date_interval_create_from_date_string("+$this->period_days days"));
            $connection->write();
            losthost\DB\DB::$pdo->commit();
        } catch (Exception $exc) {
            losthost\DB\DB::$pdo->rollBack();
            throw $exc;
        }
    }
    
    protected function isValid() {
        return (new DateTimeImmutable())->getTimestamp() <= $this->valid_till->getTimestamp();
    }
    
    public function __get($name) {
        if ($name == 'valid_till') {
            return new DateTimeImmutable(parent::__get($name));
        } else {
            return parent::__get($name);
        }
    }
    
    public function __set($name, $value) {
        if ($name == 'valid_till') {
            $value = $value->format(\losthost\DB\DB::DATE_FORMAT);
        }
        parent::__set($name, $value);
    }
}
