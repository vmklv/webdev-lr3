<?php
namespace App;

require __DIR__ . '/vendor/autoload.php';

use App\Controllers\UserController;

$user = new UserController();

$method = $_REQUEST['method'];

// var_dump($method);

$json = file_get_contents("php://input");
$data = json_decode($json);

$methods = [
    'usersrandom' => 'App\usersRandomHandle',
    'saverandomuser' => 'App\userRandomSaveHandle',
    'useradd' => 'App\userAddHandle',
    'users' => 'App\usersHandle',
    'userdel' => 'App\userDelHandle',
    'userupd' => 'App\userUpdHandle'
];

if (empty($methods[$method]) || !function_exists($methods[$method])) {
    http_response_code(405);
    var_dump(function_exists('App\\'. $methods[$method]));
    echo 'Method ' . $methods[$method] . ' doesn\'t exitst';
    return;
}

echo $methods[$method]($data);

function usersRandomHandle($data)
{
    $params = [
        'inc' => $data->inc,
        'results' => $data->results,
        'nat' => $data->nat
    ];

    $userController = new UserController();
    $users = $userController->getUsersRand($params);

    header('Content-Type: application/json');

    echo json_encode($users);
}

function userRandomSaveHandle($data)
{
    define('RANDOM_USER_FIELDS', 'name, email, login, dob');
    define('RESULTS_COUNT', 1);
    
    $paramsRandomUser = [
        'inc' => RANDOM_USER_FIELDS,
        'results' => RESULTS_COUNT
    ];

    $userController = new UserController();
    $user = $userController->getUsersRand($paramsRandomUser);

    $paramsUserToAdd = [
        'name' => $user[0]->name->first,
        'email' => $user[0]->email,
        'nickname' => $user[0]->login->username,
        'points' => $user[0]->dob->age,
    ];
    $userController->addUser($paramsUserToAdd);

    $paramsUserFromDb = new \stdClass();
    $paramsUserFromDb->email = $user[0]->email;
    $userFromDb = $userController->getUsers($paramsUserFromDb);

    header('Content-Type: application/json');

    echo json_encode($userFromDb);
}

function userAddHandle($data) 
{
    $params = [
        'name' => $data->name,
        'email' => $data->email,
        'nickname' => $data->nickname,
        'points' => $data->points,
    ];
    $userController = new UserController();

    $userController->addUser($params);
} 

function userDelHandle($data) 
{
    $userController = new UserController();
    $userController->delUser($data->id);
} 

function userUpdHandle($data) 
{
    $userController = new UserController();
    $userController->updUser($data);
} 

function usersHandle($data) {
    $params = [];

    $userController = new UserController();

    $users = $userController->getUsers($data);

    header('Content-Type: application/json');

    echo json_encode($users);
}