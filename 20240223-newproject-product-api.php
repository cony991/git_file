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

// 判斷請求類型
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 讀取產品資訊
    $sql = "SELECT * FROM product"; // 確保這是正確的表名
    $result = $conn->query($sql);

    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($products, $row);
        }
        echo json_encode(["state" => true, "data" => $products]);
    } else {
        echo json_encode(["state" => false, "message" => "未找到產品"]);
    }
} else {
    echo json_encode(["state" => false, "message" => "無效的請求方法"]);
}

$conn->close();
