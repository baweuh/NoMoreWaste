<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/User.php';

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $user->user_id = $_GET['id'];
            $user->readOne();
            if ($user->user_id != null) {
                $user_arr = array(
                    "user_id" => $user->user_id,
                    "username" => $user->username,
                    "role" => $user->role,
                    "statut" => $user->statut
                );
                http_response_code(200);
                echo json_encode($user_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "User not found."));
            }
        } else {
            $stmt = $user->read();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $users_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $user_item = array(
                        "user_id" => $row['user_id'],
                        "username" => $row['username'],
                        "role" => $row['role'],
                        "statut" => $row['statut']
                    );
                    array_push($users_arr, $user_item);
                }
                http_response_code(200);
                echo json_encode($users_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No users found."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->username) && !empty($data->password) && !empty($data->role)) {
            $user->username = $data->username;
            $user->password = password_hash($data->password, PASSWORD_BCRYPT);
            $user->role = $data->role;

            if ($user->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "User was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->user_id) && !empty($data->username) && !empty($data->role)) {
            $user->user_id = $data->user_id;
            $user->username = $data->username;
            if (!empty($data->password)) {
                $user->password = password_hash($data->password, PASSWORD_BCRYPT);
            }
            $user->role = $data->role;

            if ($user->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "User was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $user->user_id = $_GET['id'];
            if ($user->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "User was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No user ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
