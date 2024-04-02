<?php
header('Content-Type: application/json; charset=UTF-8'); // 设置头部，明确返回内容是JSON格式和字符集

// 資料庫連接設定
$servername = "localhost";
$username = "owner01";
$password = "123456";
$dbname = "testdb";

// 建立資料庫連接
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 檢查連接
if (!$conn) {
    echo json_encode(['state' => false, 'message' => '連接失敗: ' . mysqli_connect_error()]);
    exit;
}

// 設定資料庫字符集為utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// 從請求中獲取JSON數據
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 驗證輸入
if (!isset($data['memberId'], $data['isEnabled'])) {
    echo json_encode(['state' => false, 'message' => '缺少必要的參數']);
    exit;
}

// 根據isEnabled值確定Level
$levelValue = $data['isEnabled'] ? 'A200' : 'SUSPENDED'; // 假定A200為正常啟用狀態，SUSPENDED為停權

// 準備SQL語句
$sql = "UPDATE member SET Level = ? WHERE ID = ?";

$stmt = mysqli_prepare($conn, $sql);

// 綁定參數
mysqli_stmt_bind_param($stmt, "si", $levelValue, $data['memberId']);

// 執行SQL語句
if (mysqli_stmt_execute($stmt)) {
    // 檢查是否有行被更新
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['state' => true, 'message' => '會員狀態已更新']);
    } else {
        echo json_encode(['state' => false, 'message' => '未找到會員或狀態未改變']);
    }
} else {
    echo json_encode(['state' => false, 'message' => '資料庫錯誤: ' . mysqli_error($conn)]);
}

// 關閉語句和連接
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
