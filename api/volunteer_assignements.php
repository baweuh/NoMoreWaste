<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/VolunteerAssignement.php';

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

$assignment = new VolunteerAssignment($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $assignment->assignment_id = $_GET['id'];
            $assignment->readOne();
            if ($assignment->assignment_id != null) {
                $assignment_arr = array(
                    "assignment_id" => $assignment->assignment_id,
                    "volunteer_id" => $assignment->volunteer_id,
                    "service_id" => $assignment->service_id,
                    "task" => $assignment->task,
                    "date" => $assignment->date,
                    "status" => $assignment->status
                );
                http_response_code(200);
                echo json_encode($assignment_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Assignment not found."));
            }
        } else {
            $stmt = $assignment->read();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $assignments_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $assignment_item = array(
                        "assignment_id" => $row['assignment_id'],
                        "volunteer_id" => $row['volunteer_id'],
                        "service_id" => $row['service_id'],
                        "task" => $row['task'],
                        "date" => $row['date'],
                        "status" => $row['status']
                    );
                    array_push($assignments_arr, $assignment_item);
                }
                http_response_code(200);
                echo json_encode($assignments_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No assignments found."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->volunteer_id) && (!empty($data->service_id)) && !empty($data->task) && !empty($data->date)) {
            $assignment->volunteer_id = $data->volunteer_id;
            $assignment->service_id = $data->service_id;
            $assignment->task = $data->task;
            $assignment->date = $data->date;
            $assignment->status = $data->status ?? 'Pending';

            if ($assignment->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Assignment was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create assignment."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->assignment_id) && !empty($data->volunteer_id) && (!empty($data->service_id)) && !empty($data->task) && !empty($data->date)) {
            $assignment->assignment_id = $data->assignment_id;
            $assignment->volunteer_id = $data->volunteer_id;
            $assignment->service_id = $data->service_id;
            $assignment->task = $data->task;
            $assignment->date = $data->date;
            $assignment->status = $data->status;

            if ($assignment->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Assignment was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update assignment."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $assignment->assignment_id = $_GET['id'];
            if ($assignment->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Assignment was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete assignment."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No assignment ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
