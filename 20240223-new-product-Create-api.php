<?php
// 設定文件的Content-Type為JSON，這樣返回的內容將以JSON格式表示
header('Content-Type: application/json; charset=utf-8');

// 啟用錯誤顯示，方便在開發時查看錯誤信息
ini_set('display_errors', 1);
// 設定錯誤報告級別為所有可能的錯誤，以便能夠看到所有詳細錯誤信息
error_reporting(E_ALL);

// 設定資料庫連線的相關信息
$servername = "localhost"; // 資料庫伺服器地址
$username = "owner01"; // 資料庫使用者名稱
$password = "123456"; // 資料庫密碼
$dbname = "testdb"; // 資料庫名稱

// 嘗試建立到MySQL資料庫的連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    // 如果連線失敗，記錄錯誤信息到伺服器的錯誤日誌
    error_log("連接失敗: " . $conn->connect_error);
    // 向客戶端返回一個包含失敗信息的JSON對象
    echo json_encode(["state" => false, "message" => "連接失敗: " . $conn->connect_error]);
    // 結束腳本運行
    exit;
}

// 檢查HTTP請求方法是否為POST，並且檢查是否有文件被上傳
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['productImage'])) {
    // 從POST請求中獲取產品信息
    $itemNO = $_POST['itemNO']; // 產品編號
    $name = $_POST['productName']; // 產品名稱
    $price = $_POST['productPrice']; // 產品價格
    $introduce = $_POST['productIntro']; // 產品介紹

    // 文件上傳處理
    // 設定文件上傳後存儲的目錄
    $imageFolder = '/202312/images/';
    // 獲取服務器的根目錄在文件系統中的路徑，並拼接上儲存圖片的目錄
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . $imageFolder;
    // 從上傳的文件信息中取出文件名
    $file_name = basename($_FILES["productImage"]["name"]);
    // 將目標目錄和文件名拼接起來得到文件的完整儲存路徑
    $target_file = $target_dir . $file_name;

    // 檢查目標資料夾是否存在，如果不存在則創建
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 將臨時上傳的文件移動到我們的目標文件夾
    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
        // 如果文件上傳成功，則創建文件的URL
        $image_url = "http://" . $_SERVER['SERVER_NAME'] . $imageFolder . $file_name;
    } else {
        // 如果文件上傳失敗，返回錯誤信息
        echo json_encode(["state" => false, "message" => "文件上傳失敗"]);
        // 結束腳本運行
        exit;
    }

    // 準備SQL語句以插入新的產品信息
    $sql = "INSERT INTO product (itemNO, name, price, image, introduce) VALUES (?, ?, ?, ?, ?)";
    // 預處理SQL語句
    $stmt = $conn->prepare($sql);

    // 檢查是否預處理成功
    if ($stmt) {
        // 將變量綁定到準備好的語句
        $stmt->bind_param("isisi", $itemNO, $name, $price, $image_url, $introduce);
        // 執行準備好的語句
        if ($stmt->execute()) {
            // 如果執行成功，返回成功信息
            echo json_encode(["state" => true, "message" => "新記錄插入成功"]);
        } else {
            // 如果執行失敗，記錄錯誤信息到伺服器的錯誤日誌
            error_log("SQL執行錯誤: " . $stmt->error);
            // 返回錯誤信息
            echo json_encode(["state" => false, "message" => "SQL執行錯誤: " . $stmt->error]);
        }
        // 關閉準備好的語句
        $stmt->close();
    } else {
        // 如果預處理失敗，記錄錯誤信息
        error_log("SQL預備語句創建失敗");
        // 返回錯誤信息
        echo json_encode(["state" => false, "message" => "SQL預備語句創建失敗"]);
    }
} else {
    // 如果不是POST請求或沒有文件上傳，記錄錯誤信息
    error_log("非POST請求或未發現上傳的檔案");
    // 返回錯誤信息
    echo json_encode(["state" => false, "message" => "非POST請求或未發現上傳的檔案"]);
}

// 關閉資料庫連線
