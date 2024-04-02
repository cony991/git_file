<?php
header('Content-Type: application/json; charset=utf-8');

$data = file_get_contents("php://input");
error_log("Received data: " . $data); // 新增的行
if (!empty($data)) {
    $mydata = json_decode($data, true);

    if (isset($mydata["UID01"]) && !empty($mydata["UID01"])) {
        $servername = "localhost";
        $username = "owner01";
        $password = "123456";
        $dbname = "testdb";

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            http_response_code(500);
            echo json_encode(array("state" => false, "message" => "連線失敗: " . mysqli_connect_error()));
            exit;
        }

        mysqli_set_charset($conn, "utf8mb4"); // 確保數據庫連接使用UTF-8編碼

        $p_UID01 = mysqli_real_escape_string($conn, $mydata["UID01"]);
        $sql = "SELECT Username, Email, UID01, Level FROM member WHERE UID01 = '$p_UID01'";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            if (mysqli_num_rows($result) == 1) { // 確保僅在找到一條記錄時返回成功
                $userData = mysqli_fetch_assoc($result);
                echo json_encode(array("state" => true, "data" => $userData, "message" => "驗證成功, 可以登入!"));
            } else {
                echo json_encode(array("state" => false, "message" => "驗證失敗, UID不存在。"));
            }
        } else {
            echo json_encode(array("state" => false, "message" => "查詢失敗: " . mysqli_error($conn)));
        }

        mysqli_close($conn);
    } else {
        echo json_encode(array("state" => false, "message" => "傳遞參數格式錯誤!"));
    }
} else {
    echo json_encode(array("state" => false, "message" => "未傳遞任何參數!"));
}
?>