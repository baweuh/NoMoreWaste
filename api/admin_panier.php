<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Panier.php';

$database = new Database();
$db = $database->getConnection();

$panier = new Panier($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['panier_id'])) {
            $panier_id = $_GET['panier_id'];
            $stmt = $panier->readOne($panier_id);
        } else {
            $stmt = $panier->read();
        }

        $num = $stmt->rowCount();
        if ($num > 0) {
            $panier_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $panier_arr[] = array(
                    "panier_id" => $row['panier_id'],
                    "customer_id" => $row['customer_id'],
                    "product_id" => $row['product_id'],
                    "quantity" => $row['quantity'],
                    "validated" => $row['validated'],
                    "added_at" => $row['added_at'],
                );
            }
            echo json_encode(array("status" => "success", "data" => $panier_arr));
        } else {
            echo json_encode(array("status" => "error", "message" => "No records found."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->customer_id) && !empty($data->product_id) && !empty($data->quantity)) {
            $result = $panier->create($data->customer_id, $data->product_id, $data->quantity);
            echo json_encode(array("status" => $result ? "success" : "error", "message" => $result ? "Panier added." : "Failed to add panier."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->panier_id) && !empty($data->quantity)) {
            $result = $panier->update($data->panier_id, $data->quantity);
            echo json_encode(array("status" => $result ? "success" : "error", "message" => $result ? "Panier updated." : "Failed to update panier."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        if (isset($_GET['panier_id'])) {
            $result = $panier->removeProduct($_GET['panier_id']);
            echo json_encode(array("status" => $result ? "success" : "error", "message" => $result ? "Panier deleted." : "Failed to delete panier."));
        } else {
            echo json_encode(array("status" => "error", "message" => "No panier ID provided."));
        }
        break;

    default:
        echo json_encode(array("status" => "error", "message" => "Method not allowed."));
        break;
}
?>
