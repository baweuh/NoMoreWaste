<?php
// Définir les en-têtes HTTP
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Volunteer.php';

// Initialiser la base de données et l'objet Volunteer
$database = new Database();
$db = $database->getConnection();
$volunteer = new Volunteer($db);

// Déterminer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Lire un volontaire spécifique
            $volunteer->volunteer_id = $_GET['id'];
            $result = $volunteer->readOne();
            if ($result) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Volunteer not found."));
            }
        } else {
            // Lire tous les volontaires
            $stmt = $volunteer->read();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $volunteers_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $volunteers_arr[] = $row;
                }
                http_response_code(200);
                echo json_encode($volunteers_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No volunteers found."));
            }
        }
        break;

    case 'POST':
        // Créer un nouveau volontaire
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->name) && !empty($data->email) && !empty($data->phone)) {
            $volunteer->name = $data->name;
            $volunteer->email = $data->email;
            $volunteer->phone = $data->phone;
            $volunteer->skills = $data->skills ?? null;
            $volunteer->status = $data->status ?? null;

            if ($volunteer->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Volunteer was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create volunteer."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        // Mettre à jour un volontaire
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->volunteer_id) && !empty($data->name) && !empty($data->email) && !empty($data->phone)) {
            $volunteer->volunteer_id = $data->volunteer_id;
            $volunteer->name = $data->name;
            $volunteer->email = $data->email;
            $volunteer->phone = $data->phone;
            $volunteer->skills = $data->skills ?? null;
            $volunteer->status = $data->status ?? null;

            if ($volunteer->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Volunteer was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update volunteer."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        // Supprimer un volontaire
        if (isset($_GET['id'])) {
            $volunteer->volunteer_id = $_GET['id'];
            if ($volunteer->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Volunteer was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete volunteer."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No volunteer ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
