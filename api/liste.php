<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Product.php';
require_once '../class/Panier.php';
session_start();

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$panier = new Panier($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Récupérer tous les produits disponibles (par exemple pour les afficher dans une liste)
        $stmt = $product->ReadByStatus();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $product_arr = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $product_item = array(
                    "product_id" => $row['product_id'],
                    "barcode" => $row['barcode'],
                    "name" => $row['name'],
                    "quantity" => $row['quantity'],
                    "expiry_date" => $row['expiry_date'],
                    "collection_id" => $row['collection_id']
                );
                array_push($product_arr, $product_item);
            }
            http_response_code(200);
            echo json_encode($product_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No products found."));
        }
        break;

    case 'POST':
        // Assurez-vous que le customer_id est défini dans la session
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(array("message" => "Vous devez être connecté pour ajouter des produits au panier."));
            exit();
        }

        // Récupérer les données du POST
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->product_id) && !empty($data->quantity)) {
            $product_id = htmlspecialchars(strip_tags($data->product_id));
            $quantity = htmlspecialchars(strip_tags($data->quantity));
            $customer_id = $_SESSION['user_id']; // Utiliser l'ID de l'utilisateur de la session

            // Vérifier si le produit existe et a suffisamment de stock
            $product->product_id = $product_id;
            $product->readOne(); // Méthode pour lire les détails d'un produit

            if ($product->quantity > $quantity) {
                http_response_code(400);
                echo json_encode(array("message" => "Quantité demandée dépasse le stock disponible."));
                exit();
            }

            // Ajouter le produit au panier
            $panier->customer_id = $customer_id;
            $panier->product_id = $product_id;
            $panier->quantity = $quantity;

            if ($panier->addToCart()) {
                http_response_code(200);
                echo json_encode(array("message" => "Produit ajouté au panier avec succès."));
            } else {
                // En cas d'erreur, annuler la transaction
                http_response_code(503);
                echo json_encode(array("message" => "Impossible d'ajouter le produit au panier. "));
            }

        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Données manquantes. Assurez-vous de fournir product_id et quantity."));
        }
        break;

    case 'DELETE':
        // Suppression d'un produit du panier
        $panier_id = isset($_GET['panier_id']) ? $_GET['panier_id'] : null;

        if($panier_id !== null) {
            $panier->panier_id = $panier_id;
            if($panier->removeProduct()) {
                http_response_code(200);
                echo json_encode(array("message" => "Product removed from cart successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to remove product from cart."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data. Panier ID is required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
