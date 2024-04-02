<?php
    // 資料庫連接設定
    $servername = "localhost";
    $username = "owner01";
    $password = "123456";
    $dbname = "testdb";

    // 建立資料庫連接
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // 檢查連接
    if (!$conn) {
        die("連線失敗: " . mysqli_connect_error());
    }

    // 定義查詢語句
    $sql = "SELECT ID, Username, Level FROM member";

    // 執行查詢並檢查結果
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // 初始化一個空陣列來儲存查詢結果
        $members = array();

        // 輸出每一行數據
        while($row = mysqli_fetch_assoc($result)) {
            $members[] = $row;
        }

        // 將結果編碼成JSON格式並輸出
        echo json_encode(array("state" => true, "data" => $members, "message" => "查詢資料成功!"));
    } else {
        echo json_encode(array("state" => false, "message" => "查無資料!"));
    }

    // 關閉資料庫連接
    mysqli_close($conn);
?>