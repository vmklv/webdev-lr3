<?php

namespace App\Controllers;

use \DB;

class DbController
{
    const DB_USER = 'root';
    const DB_PASSWORD = 'root';

    public function createDbInstance(string $dbName)
    {
        DB::$user = self::DB_USER;
        DB::$password = self::DB_PASSWORD;
        DB::$dbName = $dbName;
    }

    public function delUser($id)
    {
        DB::delete('users', 'id=%s', $id);
    }
}