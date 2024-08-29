<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Delivery.php';
session_start();

$database = new Database();
$db = $database->getConnection();
$tournee = new Delivery($db);

$method = $_SERVER['REQUEST_METHOD'];
$customer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

switch ($method) {
    case 'GET':
        // Lire les tournées
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        if ($customer_id === null) {
            http_response_code(403);
            echo json_encode(array("message" => "User not authenticated."));
            exit();
        }

        if ($status !== null) {
            $stmt = $tournee->readByStatus($customer_id, $status);
        } else {
            $stmt = $tournee->readAll($customer_id);
        }

        $num = $stmt->rowCount();

        if ($num > 0) {
            $tournees_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tournees_arr[] = array(
                    "delivery_id" => $row['delivery_id'],
                    "delivery_date" => $row['delivery_date'],
                    "recipient_type" => $row['recipient_type'],
                    "customer_id" => $row['customer_id'],
                    "status" => $row['status'],
                    "pdf_report_path" => $row['pdf_report_path']
                );
            }
            http_response_code(200);
            echo json_encode($tournees_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No deliveries found."));
        }
        break;

    case 'DELETE':
        // Annuler une tournée
        parse_str(file_get_contents("php://input"), $data);

        if (!isset($data['delivery_id'])) {
            http_response_code(400);
            echo json_encode(array("message" => "Delivery ID is required."));
            exit();
        }

        $delivery_id = $data['delivery_id'];

        if ($tournee->cancelDelivery($delivery_id, $customer_id)) {
            http_response_code(200);
            echo json_encode(array("message" => "Delivery successfully canceled."));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Unable to cancel delivery."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
