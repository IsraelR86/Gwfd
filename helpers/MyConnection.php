<?php
namespace app\helpers;

use Yii;
use yii\db\Connection;

class MyConnection extends Connection
{
    /**
     * Se sobreescribe esta función para establecer el 
     * time_zone = "America/Mexico_City"
     */
    protected function initConnection()
    {
        parent::initConnection();
        
        //$this->pdo->exec('SET time_zone = "America/Mexico_City"');
        $this->pdo->exec('SET time_zone = "-6:00"');
    }
}
