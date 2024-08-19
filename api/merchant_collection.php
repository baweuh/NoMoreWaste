<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Collection.php';
session_start(); // Assurez-vous que la session est démarrée

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied."));
    exit();
}

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

$collection = new Collection($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Lire une collecte spécifique
            $collection->collection_id = $_GET['id'];
            $collection->readOne();
            if ($collection->collection_id != null && $collection->merchant_id == $_SESSION['user_id']) {
                $collection_arr = array(
                    "collection_id" => $collection->collection_id,
                    "merchant_id" => $collection->merchant_id,
                    "name" => $collection->name,
                    "collection_date" => $collection->collection_date,
                    "total_items" => $collection->total_items,
                    "status" => $collection->status,
                    "created_at" => $collection->created_at,
                    "updated_at" => $collection->updated_at
                );
                http_response_code(200);
                echo json_encode($collection_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Collection not found or access denied."));
            }
        } else {
            // Lire les collectes en fonction du rôle
            if ($_SESSION['role'] === 'merchant') {
                $merchant_id = $_SESSION['user_id'];
                $stmt = $collection->readByMerchantId($merchant_id);
            } else {
                $stmt = $collection->read();
            }

            $num = $stmt->rowCount();
            if ($num > 0) {
                $collections_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $collection_item = array(
                        "collection_id" => $row['collection_id'],
                        "merchant_id" => $row['merchant_id'],
                        "name" => $row['name'],
                        "collection_date" => $row['collection_date'],
                        "total_items" => $row['total_items'],
                        "status" => $row['status'],
                        "created_at" => $row['created_at'],
                        "updated_at" => $row['updated_at']
                    );
                    array_push($collections_arr, $collection_item);
                }
                http_response_code(200);
                echo json_encode($collections_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No collections found."));
            }
        }
        break;

    case 'POST':
        // Création d'une nouvelle collecte
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->name) && !empty($data->collection_date) && !empty($data->total_items)) {
            $collection->merchant_id = $_SESSION['user_id']; // Utiliser l'ID du commerçant de la session
            $collection->name = $data->name;
            $collection->collection_date = $data->collection_date;
            $collection->total_items = $data->total_items;
            $collection->status = $data->status ?? 'pending'; // Statut par défaut à 'pending'

            if ($collection->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Collection was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create collection."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data.", "data" => $data));
        }
        break;

    case 'PUT':
        // Mise à jour d'une collecte existante
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid JSON format."));
            break;
        }

        if (!empty($data->collection_id) && !empty($data->name) && !empty($data->collection_date) && !empty($data->total_items)) {
            $collection->collection_id = $data->collection_id;
            $collection->merchant_id = $_SESSION['user_id']; // Utiliser l'ID du commerçant de la session
            $collection->name = $data->name;
            $collection->collection_date = $data->collection_date;
            $collection->total_items = $data->total_items;
            $collection->status = $data->status ?? '0'; // Statut par défaut à '0'

            if ($collection->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Collection was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update collection."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data. Please provide all required fields."));
        }
        break;

    case 'DELETE':
        // Suppression d'une collecte
        if (isset($_GET['id'])) {
            $collection->collection_id = $_GET['id'];
            $collection->merchant_id = $_SESSION['user_id']; // Utiliser l'ID du commerçant de la session
            if ($collection->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Collection was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete collection."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No collection ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
