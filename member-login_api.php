<?php
//input: {"Username":"XX", "Password":"XXX"}
// {"state" : true, "data": "登入後的帳號資料(密碼除外)", "message" : "登入成功!"}
// {"state" : false, "message" : "登入失敗!"}
// {"state" : false, "message" : "傳遞參數格式錯誤!"}
// {"state" : false, "message" : "未傳遞任何參數!"}

$data = file_get_contents("php://input", "r");
if ($data != "") {
    $mydata = array();
    $mydata = json_decode($data, true);
    if (isset($mydata["Username"]) && isset($mydata["Password"]) && $mydata["Username"] != "" && $mydata["Password"] != "") {
        $p_Username = $mydata["Username"];
        $p_Password = $mydata["Password"];


        $servername = "localhost";
        $username = "owner01";
        $password = "123456";
        $dbname = "testdb";

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            die("連線失敗" . mysqli_connect_error());
        }
        // 撈出帳號密碼信箱,第一次撈資料
        $sql = "SELECT Username, Password, Email FROM member WHERE Username = '$p_Username'";
        $result = mysqli_query($conn, $sql); //執行 SQL 查詢，並將查詢結果存儲在 $result 變數中。
        if (mysqli_num_rows($result) == 1) {  //檢查查詢結果中的行數是否等於 1。
            //確認帳號符合, 密碼不確定
            $row = mysqli_fetch_assoc($result);
            if (password_verify($p_Password, $row["Password"])) {
                //密碼比對正確, 撈取不包含密碼的使用者資料並產生uid
                $uid = substr(hash("sha256", uniqid(time())), 0, 8);
                //更新uid至資料庫
                $sql = "UPDATE member SET UID01 = '$uid' WHERE Username = '$p_Username'";
                if (mysqli_query($conn, $sql)) {
                    $sql = "SELECT Username, Email, UID01, Level FROM member WHERE Username = '$p_Username'";
                    $result = mysqli_query($conn, $sql);
                    $result = mysqli_query($conn, $sql);  //從資料庫查詢結果中取出一行資料以陣列方式儲存在 $row
                    $row = mysqli_fetch_assoc($result);
                    $mydata = array();  //創建了一個空陣列 $mydata
                    $mydata[] = $row;  //$row 變數添加到 $mydata 這個陣列

                    echo '{"state" : true, "data": ' . json_encode($mydata) . ', "message" : "登入成功!"}';
                } else {
                    //uid更新錯誤
                    echo '{"state" : false, "message" : "登入失敗, uid更新錯誤"}';
                }
            } else {
                //密碼比對錯誤
                echo '{"state" : false, "message" : "登入失敗"}';
            }
        } else {
            //確認帳號不符合, 登入失敗
            echo '{"state" : false, "message" : "登入失敗"}';
        }
        mysqli_close($conn);
    } else {
        echo '{"state" : false, "message" : "傳遞參數格式錯誤!"}';
    }
} else {
    echo '{"state" : false, "message" : "未傳遞任何參數!"}';
}
