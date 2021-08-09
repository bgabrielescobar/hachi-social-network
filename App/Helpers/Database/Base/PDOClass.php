<?php 

namespace App\Helpers\Database\Base;

use PDO;
use App\Config\Settings;

class PDOClass {
    
    protected $pdo;

    public function __construct()
    {
            $this->pdo = new PDO(
            'mysql:' .
                'host=' . Settings::getSetting('db')['host'] .
                ';dbname=' . Settings::getSetting('db')['db-name'],
                Settings::getSetting('db')['user-name'],
                Settings::getSetting('db')['password']
            );

    }

}