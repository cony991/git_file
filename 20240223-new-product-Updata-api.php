<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// 設定資料庫連線參數
$servername = "localhost";
$username = "owner01";
$password = "123456";
$dbname = "testdb";

// 建立與資料庫的連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查資料庫連線是否成功
if ($conn->connect_error) {
    die(json_encode(["state" => false, "message" => "連接失敗: " . $conn->connect_error]));
}

// 確認是否為POST請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 檢查是否有'action'參數
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // 根據'action'參數的值執行對應操作
        if ($action === 'update') {
            // 更新產品
            $itemNO = intval($_POST['itemNO']);
            $name = $_POST['productName'];
            $price = intval($_POST['productPrice']);
            $introduce = $_POST['productIntro'];
            $image = ''; // 預設圖片URL為空

            // 處理文件上傳
            if (isset($_FILES['productImage'])) {
                $imageFolder = '/202312/images/';
                $target_dir = $_SERVER['DOCUMENT_ROOT'] . $imageFolder;
                $file_name = basename($_FILES["productImage"]["name"]);
                $target_file = $target_dir . $file_name;

                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
                    $image = "http://" . $_SERVER['SERVER_NAME'] . $imageFolder . $file_name;
                } else {
                    echo json_encode(["state" => false, "message" => "文件上傳失敗"]);
                    exit;
                }
            }

            // 準備SQL更新語句
            $sql = "UPDATE product SET name=?, price=?, image=?, introduce=? WHERE itemNO=?";
            $stmt = $conn->prepare($sql);

            // 嘗試執行更新操作
            if ($stmt) {
                $stmt->bind_param("sisii", $name, $price, $image, $introduce, $itemNO);
                if ($stmt->execute()) {
                    echo json_encode(["state" => true, "message" => "產品更新成功"]);
                } else {
                    echo json_encode(["state" => false, "message" => "更新錯誤: " . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(["state" => false, "message" => "SQL預備語句創建失敗"]);
            }
        } elseif ($action === 'delete') {
            // 刪除產品
            $itemNO = intval($_POST['itemNO']);

            // 準備SQL刪除語句
            $sql = "DELETE FROM product WHERE itemNO=?";
            $stmt = $conn->prepare($sql);

            // 嘗試執行刪除操作
            if ($stmt) {
                $stmt->bind_param("i", $itemNO);
                if ($stmt->execute()) {
                    echo json_encode(["state" => true, "message" => "產品刪除成功"]);
                } else {
                    echo json_encode(["state" => false, "message" => "刪除錯誤: " . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(["state" => false, "message" => "SQL預備語句創建失敗"]);
            }
        } else {
            echo json_encode(["state" => false, "message" => "未知的操作"]);
        }
    } else {
        echo json_encode(["state" => false, "message" => "操作參數未設定"]);
    }
} else {
    echo json_encode(["state" => false, "message" => "只支持POST請求"]);
}

// 關閉資料庫連線
$conn->close();
