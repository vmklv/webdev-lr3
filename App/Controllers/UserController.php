<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use App\Controllers\DbController;
use \DB;


class UserController
{
    const USERS_API_URL = 'https://randomuser.me/api';
    const DB_NAME = 'phpapi';
    const DB_USER_TABLE = 'users';

    public function getUsersRand(array $params = []): array
    {
        $client = $this->getHttpClient();

        $responseContent = $client->request('GET', '', [
            'query' => $params])->getBody()->getContents();

        $data = json_decode($responseContent);

        return $data->results;
    }

    private function getHttpClient()
    {
        return new Client([
            'base_uri' => self::USERS_API_URL,
        ]);
    }

    public function addUser(array $params = [])
    {
        $dbController = new DbController();
        $dbController->createDbInstance(self::DB_NAME);

        DB::insert(self::DB_USER_TABLE, $params);
    }

    public function delUser($id)
    {
        $dbController = new DbController();
        $dbController->createDbInstance(self::DB_NAME);
        $dbController->delUser($id);
    }

    public function getUsers(object $params)
    {
        $dbController = new DbController();
        $dbController->createDbInstance(self::DB_NAME);

        $where = (!empty($params)) ? ' WHERE ' : '';
        $countParams = 0;
        foreach($params as $key => $value) {
            if ($countParams > 0) {
                $where .= ' AND ';
            }
            $where .= $key . ' = "' . $value . '"';
            $countParams++;
        }

        return DB::query("SELECT name, points, email FROM " . self::DB_USER_TABLE . " {$where}");
    }

    public function updUser(object $params)
    {
        $dbController = new DbController();
        $dbController->createDbInstance(self::DB_NAME);

        if (empty($params->id))
            return false;
        $upd_user = (array) $params;
        unset($upd_user["id"]);

        return DB::update('users', $upd_user, "id=%s", $params->id);
    }
}