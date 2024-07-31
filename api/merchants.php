<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../includes/Database.php';
require_once '../class/Merchant.php';

$database = new Database();
$db = $database->getConnection();

$merchant = new Merchant($db);

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $merchant->merchant_id = $_GET['id'];
            $merchant->readOne();
            if($merchant->name != null) {
                $merchant_arr = array(
                    "merchant_id" => $merchant->merchant_id,
                    "name" => $merchant->name,
                    "address" => $merchant->address,
                    "phone" => $merchant->phone,
                    "email" => $merchant->email,
                    "membership_start_date" => $merchant->membership_start_date,
                    "membership_end_date" => $merchant->membership_end_date,
                    "renewal_reminder_sent" => $merchant->renewal_reminder_sent
                );
                http_response_code(200);
                echo json_encode($merchant_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Merchant not found."));
            }
        } else {
            $stmt = $merchant->read();
            $num = $stmt->rowCount();
            if($num > 0) {
                $merchants_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $merchant_item = array(
                        "merchant_id" => $merchant_id,
                        "name" => $name,
                        "address" => $address,
                        "phone" => $phone,
                        "email" => $email,
                        "membership_start_date" => $membership_start_date,
                        "membership_end_date" => $membership_end_date,
                        "renewal_reminder_sent" => $renewal_reminder_sent
                    );
                    array_push($merchants_arr, $merchant_item);
                }
                http_response_code(200);
                echo json_encode($merchants_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No merchants found."));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->name) && !empty($data->address) && !empty($data->phone) && !empty($data->email) && !empty($data->membership_start_date) && !empty($data->membership_end_date)) {
            $merchant->name = $data->name;
            $merchant->address = $data->address;
            $merchant->phone = $data->phone;
            $merchant->email = $data->email;
            $merchant->membership_start_date = $data->membership_start_date;
            $merchant->membership_end_date = $data->membership_end_date;
            $merchant->renewal_reminder_sent = $data->renewal_reminder_sent;
            if($merchant->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Merchant was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create merchant."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->merchant_id) && !empty($data->name) && !empty($data->address) && !empty($data->phone) && !empty($data->email) && !empty($data->membership_start_date) && !empty($data->membership_end_date)) {
            $merchant->merchant_id = $data->merchant_id;
            $merchant->name = $data->name;
            $merchant->address = $data->address;
            $merchant->phone = $data->phone;
            $merchant->email = $data->email;
            $merchant->membership_start_date = $data->membership_start_date;
            $merchant->membership_end_date = $data->membership_end_date;
            $merchant->renewal_reminder_sent = $data->renewal_reminder_sent;
            if($merchant->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Merchant was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update merchant."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'DELETE':
        if(isset($_GET['id'])) {
            $merchant->merchant_id = $_GET['id'];
            if($merchant->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Merchant was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete merchant."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No merchant ID provided."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
