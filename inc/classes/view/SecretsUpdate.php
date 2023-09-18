<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of vipw
 *
 * @author drweb
 */
class SecretsUpdate extends losthost\DB\DBView {

    protected $user;
    protected $host;
    protected $fingerprint;
    protected $public_key;
    protected $private_key;
    
    public function __construct(string $user, string $host, string $fingerprint, $public_key, $private_key) {
    
        $this->user = $user;
        $this->host = $host;
        $this->fingerprint = $fingerprint;
        $this->public_key = $public_key;
        $this->private_key = $private_key;
            
        $sql = <<<END
                SELECT
                    login, password
                FROM
                    [connections]
                WHERE
                    is_active = 1
                    AND active_till >= ?
                END;
                    
        parent::__construct($sql, date_create_immutable()->format(\losthost\DB\DB::DATE_FORMAT));
    }
    
    public function getSecrets() {
        global $config;
        
        $secrets = 'connect.losthost.online : RSA "privkey.pem"';
        
        while ($this->next()) {
            $secrets .= "\n$this->login : EAP \"$this->password\"";
        }
        return "$secrets\n";
    }
    
    public function update() {
        global $config;
        
        $key = \phpseclib3\Crypt\PublicKeyLoader::load(file_get_contents($this->private_key));
        
        $sftp = new \phpseclib3\Net\SFTP($this->host);
        $sftp_fingerprint = $sftp->getServerPublicHostKey();
        if ($this->fingerprint != $sftp_fingerprint) {
            throw new \Exception('Invid host fingerprint. Got '. $sftp_fingerprint); }

        if (!$sftp->login($this->user, $key)) {
            throw new \Exception('Not authenticated with given key file(s)');   }
        
        if (!$sftp->put($config['ipsec_secrets'], $this->getSecrets())) {
            throw new \Exception('Can not update '. $config['ipsec_secrets']);      }
        
        $ssh = new phpseclib3\Net\SSH2($this->host);
        $ssh_fingerprint = $ssh->getServerPublicHostKey();
        if ($this->fingerprint != $ssh_fingerprint) {
            throw new \Exception('Invid host fingerprint. Got '. $ssh_fingerprint);  }
        
        $ssh->exec($config['ipsec_command']);
        if ($ssh->getExitStatus()) {
            throw new \Exception('Can not exec '. $config['ipsec_command']);                 }
    }
}
