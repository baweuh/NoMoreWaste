<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Product.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied."));
    exit();
}

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Requête pour un produit spécifique
            $product_id = $_GET['id'];
            $product->product_id = $product_id;
            $stmt = $product->readOne(); // Assurez-vous que la méthode readOne existe et fonctionne correctement

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $product_item = array(
                    "product_id" => $row['product_id'],
                    "barcode" => $row['barcode'],
                    "name" => $row['name'],
                    "quantity" => $row['quantity'],
                    "expiry_date" => $row['expiry_date'],
                    "collection_id" => $row['collection_id']
                );
                http_response_code(200);
                echo json_encode($product_item);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Product not found."));
            }
        } else {
            // Code pour récupérer tous les produits pour le marchand
            if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'commercants') {
                $merchant_id = $_SESSION['user_id'];
                $stmt = $product->readByMerchantId($merchant_id);
                $num = $stmt->rowCount();
                if ($num > 0) {
                    $products_arr = array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $product_item = array(
                            "product_id" => $row['product_id'],
                            "barcode" => $row['barcode'],
                            "name" => $row['name'],
                            "quantity" => $row['quantity'],
                            "expiry_date" => $row['expiry_date'],
                            "collection_id" => $row['collection_id'],
                            "collection_name" => $row['collection_name'],
                            "collection_date" => $row['collection_date'],
                            "total_items" => $row['total_items']
                        );
                        array_push($products_arr, $product_item);
                    }
                    http_response_code(200);
                    echo json_encode($products_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "No products found for this merchant."));
                }
            } else {
                http_response_code(403);
                echo json_encode(array("message" => "Unauthorized access."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->barcode) && !empty($data->name) && !empty($data->quantity) && !empty($data->collection_id)) {
            $product->barcode = $data->barcode;
            $product->name = $data->name;
            $product->quantity = $data->quantity;
            $product->expiry_date = $data->expiry_date;
            $product->collection_id = $data->collection_id;

            if ($product->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Product was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid JSON format."));
            break;
        }

        if (!empty($data->product_id) && !empty($data->barcode) && !empty($data->name) && !empty($data->quantity) && !empty($data->collection_id)) {
            $product->product_id = $data->product_id;
            $product->barcode = $data->barcode;
            $product->name = $data->name;
            $product->quantity = $data->quantity;
            $product->expiry_date = $data->expiry_date;
            $product->collection_id = $data->collection_id;

            if ($product->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Product was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->product_id)) {
            $product->product_id = $data->product_id;

            if ($product->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Product was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
