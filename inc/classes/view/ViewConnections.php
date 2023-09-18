<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ViewConnections
 *
 * @author drweb
 */
class ViewConnections extends losthost\DB\DBView {
    public function __construct() {
        $sql = <<<END
                SELECT 
                    login,
                    description
                FROM 
                    [connections]
                WHERE 
                    user = ?
                END;
        parent::__construct($sql, Context::$user->id);
    }
    
    public function asString() {
        return "/$this->login: $this->description";
    }
    
    public function show() {
        $this->reset();
        $result = [];
 
        while ($this->next()) {
            $result[] = $this->asString();
        }
        
        return implode("\n", $result);
    }
}
