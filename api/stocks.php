<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Stock.php';

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

$stock = new Stock($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Lecture d'un stock spécifique
            $stock->stock_id = $_GET['id'];
            $stock->readOne();
            if ($stock->stock_id != null) {
                $stock_arr = array(
                    "stock_id" => $stock->stock_id,
                    "product_id" => $stock->product_id,
                    "quantity" => $stock->quantity,
                    "location" => $stock->location
                );
                http_response_code(200);
                echo json_encode($stock_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Stock not found."));
            }
        } else {
            // Lecture de tous les stocks
            $stmt = $stock->read();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $stocks_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $stock_item = array(
                        "stock_id" => $row['stock_id'],
                        "product_id" => $row['product_id'],
                        "quantity" => $row['quantity'],
                        "location" => $row['location']
                    );
                    array_push($stocks_arr, $stock_item);
                }
                http_response_code(200);
                echo json_encode($stocks_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No stocks found."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->product_id) && !empty($data->quantity) && !empty($data->location)) {
            $stock->product_id = $data->product_id;
            $stock->quantity = $data->quantity;
            $stock->location = $data->location;

            if ($stock->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Stock was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create stock."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        // Mise à jour d'un stock existant
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->stock_id) && !empty($data->product_id) && !empty($data->quantity) && !empty($data->location)) {
            $stock->stock_id = $data->stock_id;
            $stock->product_id = $data->product_id;
            $stock->quantity = $data->quantity;
            $stock->location = $data->location;

            if ($stock->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Stock was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update stock."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        // Suppression d'un stock
        if (isset($_GET['id'])) {
            $stock->stock_id = $_GET['id'];
            if ($stock->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Stock was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete stock."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No stock ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
