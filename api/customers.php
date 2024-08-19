<?php
// Inclure les fichiers nécessaires
require_once '../includes/Database.php';
require_once '../class/Customer.php';

// Initialisation de la base de données et de la connexion
$database = new Database();
$db = $database->getConnection();

// Création de l'objet Customer avec la connexion
$customer = new Customer($db);

// Détermination de la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $customer->customer_id = $_GET['id'];
            $customer->readOne();
            if ($customer->name != null) {
                echo json_encode(array(
                    "customer_id" => $customer->customer_id,
                    "name" => $customer->name,
                    "email" => $customer->email,
                    "phone" => $customer->phone
                ));
            } else {
                echo json_encode(array("message" => "Customer not found."));
            }
        } else {
            $stmt = $customer->read();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $customers_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $customer_item = array(
                        "customer_id" => $row['customer_id'],
                        "name" => $row['name'],
                        "email" => $row['email'],
                        "phone" => $row['phone']
                    );
                    array_push($customers_arr, $customer_item);
                }
                echo json_encode($customers_arr);
            } else {
                echo json_encode(array("message" => "No customers found."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (
            !empty($data->name) &&
            !empty($data->email) &&
            !empty($data->phone)
        ) {
            $customer->name = $data->name;
            $customer->email = $data->email;
            $customer->phone = $data->phone;
            if ($customer->create()) {
                echo json_encode(array("message" => "Customer created."));
            } else {
                echo json_encode(array("message" => "Unable to create customer."));
            }
        } else {
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (
            !empty($data->customer_id) &&
            (!empty($data->name) || !empty($data->email) || !empty($data->phone))
        ) {
            $customer->customer_id = $data->customer_id;
            $customer->name = $data->name ?? $customer->name;
            $customer->email = $data->email ?? $customer->email;
            $customer->phone = $data->phone ?? $customer->phone;
            if ($customer->update()) {
                echo json_encode(array("message" => "Customer updated."));
            } else {
                echo json_encode(array("message" => "Unable to update customer."));
            }
        } else {
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $customer->customer_id = $_GET['id'];
            if ($customer->delete()) {
                echo json_encode(array("message" => "Customer deleted."));
            } else {
                echo json_encode(array("message" => "Unable to delete customer."));
            }
        } else {
            echo json_encode(array("message" => "No customer ID provided."));
        }
        break;

    default:
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
