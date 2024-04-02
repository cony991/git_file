<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$servername = "localhost";
$username = "owner01";
$password = "123456";
$dbname = "testdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["state" => false, "message" => "連接失敗: " . $conn->connect_error]);
    exit;
}

// 處理 GET 請求
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 檢查是否有訂單相關的參數
    if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
        $product_id = $conn->real_escape_string($_GET['product_id']);
        $quantity = $conn->real_escape_string($_GET['quantity']);

        // 基本驗證
        if (!is_numeric($product_id) || !is_numeric($quantity)) {
            echo json_encode(["state" => false, "message" => "無效的產品ID或數量。"]);
            exit;
        }

        // 插入訂單資料到 `orders` 表
        $sql = "INSERT INTO orders (product_id, quantity) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["state" => false, "message" => "準備語句失敗: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("ii", $product_id, $quantity);

        if ($stmt->execute()) {
            echo json_encode(["state" => true, "message" => "訂單已成功建立。"]);
        } else {
            echo json_encode(["state" => false, "message" => "建立訂單失敗: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["state" => false, "message" => "缺少產品ID或數量參數。"]);
    }
} else {
    echo json_encode(["state" => false, "message" => "不支持的請求方法。"]);
}

$conn->close();
