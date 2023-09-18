<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of DBLock
 *
 * @author drweb
 */
class DBLock extends \losthost\DB\DBObject {
    
    const TABLE_NAME = 'locks';
    
    const SQL_CREATE_TABLE = <<<END
            CREATE TABLE IF NOT EXISTS %TABLE_NAME% (
                name varchar(256) NOT NULL COMMENT 'Имя_Объекта:Секрет', 
                until int(11) COMMENT 'Время удаления по таймауту',
                PRIMARY KEY (name)
            ) COMMENT = 'v1.0.0' 
            END;

    public function __construct(string $name, int $timeout) {
        
        parent::__construct();
        $sth = $this->prepare("DELETE FROM %TABLE_NAME% WHERE name = ? AND until < ?");
        $until = $timeout + time();
        
        $this->name = $name;
        $this->until = $until;
        
        for ($i = 0; $i < $timeout; $i++) {
            try {
                $sth->execute([$name, $until]);
                $this->insert('', null);
                return;
            } catch (\Exception $e) {
                echo $e->getMessage(). "\n";
                sleep(1);
            }
        }
        
        throw new \Exception("Can not lock $name");
    }
}
