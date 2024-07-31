<?php
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];
$langDir = '../lang/';

switch ($method) {
    case 'GET':
        if (isset($_GET['code'])) {
            $langCode = $_GET['code'];
            $filePath = $langDir . $langCode . '.json';

            if (file_exists($filePath)) {
                echo file_get_contents($filePath);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Language file not found"]);
            }
        } else {
            $files = glob($langDir . '*.json');
            $languages = [];

            foreach ($files as $file) {
                $code = basename($file, '.json');
                $content = json_decode(file_get_contents($file), true);
                $name = $content['language_name'] ?? $code;
                $languages[] = ['code' => $code, 'name' => $name];
            }

            echo json_encode($languages);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['code']) && isset($data['content'])) {
            $filePath = $langDir . $data['code'] . '.json';
            if (file_put_contents($filePath, json_encode($data['content'], JSON_PRETTY_PRINT))) {
                echo json_encode(["message" => "Language file created"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to create language file"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['code']) && isset($data['content'])) {
            $filePath = $langDir . $data['code'] . '.json';
            if (file_exists($filePath)) {
                if (file_put_contents($filePath, json_encode($data['content'], JSON_PRETTY_PRINT))) {
                    echo json_encode(["message" => "Language file updated"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to update language file"]);
                }
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Language file not found"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['code'])) {
            $filePath = $langDir . $_GET['code'] . '.json';
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    echo json_encode(["message" => "Language file deleted"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to delete language file"]);
                }
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Language file not found"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Language code not provided"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
