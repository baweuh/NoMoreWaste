<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Panier.php';
require_once '../class/Product.php';
require_once '../class/Delivery.php';
session_start();

$database = new Database();
$db = $database->getConnection();

$panier = new Panier($db);
$product = new Product($db);
$delivery = new Delivery($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $panier_id = $_GET['id'];
            $panier->panier_id = $panier_id;
            $stmt = $panier->ReadOne($panier_id);

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $panier_arr = array(
                    "panier_id" => $panier->panier_id,
                    "customer_id" => $panier->customer_id,
                    "product_id" => $panier->product_id,
                    "name" => $product->name,
                    "expiry_date" => $product->expiry_date,
                    "validated" => $panier->validated,
                    "quantity" => $panier->quantity
                );
                http_response_code(200);
                echo json_encode(array($panier_arr));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Panier not found"));
            }
        } else {
            // Code pour récupérer tous les produits pour le marchand
            if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'clients') {
                $customer_id = $_SESSION['user_id'];
                $stmt = $panier->ReadByCustomerId($customer_id);
                $num = $stmt->rowCount();
                if ($num > 0) {
                    $paniers_arr = array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $panier_item = array(
                            "panier_id" => $row['panier_id'],
                            "customer_id" => $row['customer_id'],
                            "product_id" => $row['product_id'],
                            "name" => $row['name'],
                            "expiry_date" => $row['expiry_date'],
                            "validated" => $row['validated'],
                            "quantity" => $row['quantity']
                        );
                        array_push($paniers_arr, $panier_item);
                    }
                    http_response_code(200);
                    echo json_encode($paniers_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "No paniers found for this customer."));
                }
            } else {
                http_response_code(403);
                echo json_encode(array("message" => "Unauthorized access."));
            }
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $panier->panier_id = $_GET['id'];
            if ($panier->removeProduct()) {
                http_response_code(200);
                echo json_encode(array("message" => "Product removed from Panier"));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to remove product"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Method not allowed."));
        }
        break;
}
?>
