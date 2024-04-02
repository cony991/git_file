<?php
    // 資料庫設定
    $servername = "localhost";
    $username = "owner01";
    $password = "123456";
    $dbname = "testdb";

    // 建立與資料庫的連線
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // 檢查連線
    if(!$conn){
        die("連線失敗: " . mysqli_connect_error());
    }

    // 從php://input獲取JSON格式的原始POST數據
    $data = json_decode(file_get_contents("php://input"), true);

    if(isset($data["memberId"]) && isset($data["newLevel"])){
        // 清理數據以防止SQL注入
        $memberId = mysqli_real_escape_string($conn, $data["memberId"]);
        $newLevel = mysqli_real_escape_string($conn, $data["newLevel"]);

        // 準備SQL語句來更新會員等級
        $sql = "UPDATE member SET level='$newLevel' WHERE ID='$memberId'";

        // 執行SQL語句
        if(mysqli_query($conn, $sql)){
            if(mysqli_affected_rows($conn) > 0){
                echo json_encode(array("state" => true, "message" => "會員等級更新成功!"));
            } else {
                echo json_encode(array("state" => false, "message" => "沒有變更的會員等級或找不到指定會員。"));
            }
        } else {
            echo json_encode(array("state" => false, "message" => "更新會員等級失敗: " . mysqli_error($conn)));
        }
    } else {
        echo json_encode(array("state" => false, "message" => "缺少必要的參數。"));
    }

    // 關閉連線
    mysqli_close($conn);
?>