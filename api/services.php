<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Service.php';

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $service->service_id = $_GET['id'];
            $service->readOne();
            if ($service->service_id != null) {
                $service_arr = array(
                    "service_id" => $service->service_id,
                    "name" => $service->name,
                    "description" => $service->description,
                );
                http_response_code(200);
                echo json_encode($service_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Service not found."));
            }
        } else {
            $stmt = $service->read();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $services_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $service_item = array(
                        "service_id" => $row['service_id'],
                        "name" => $row['name'],
                        "description" => $row['description'],
                    );
                    array_push($services_arr, $service_item);
                }
                http_response_code(200);
                echo json_encode($services_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No services found."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->name) && !empty($data->description)) {
            $service->name = $data->name;
            $service->description = $data->description;

            if ($service->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Service was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create service."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data.", "data" => $data));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->service_id) && !empty($data->name) && !empty($data->description)) {
            $service->service_id = $data->service_id;
            $service->name = $data->name;
            $service->description = $data->description;

            if ($service->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Service was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update service."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $service->service_id = $_GET['id'];
            if ($service->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Service was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete service."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No service ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
