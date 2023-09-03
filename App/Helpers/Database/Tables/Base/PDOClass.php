<?php 

namespace App\Helpers\Database\Tables\Base;

use PDO;
use App\Config\Settings;

class PDOClass {
    
    protected $pdo;

    public function __construct()
    {
            $this->pdo = new PDO(
            'mysql:' .
                'host=' . Settings::get('HOST') .
                ';dbname=' . Settings::get('DB_NAME'),
                Settings::get('USER_NAME'),
                Settings::get('PASSWORD')
            );

    }

}