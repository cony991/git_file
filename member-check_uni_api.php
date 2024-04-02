<?php
    //input: {"Username":"XX"}
    // {"state" : true, "message" : "帳號不存在, 可以使用!"}
    // {"state" : false, "message" : "帳號已存在,不可以使用!"}
    // {"state" : false, "message" : "傳遞參數格式錯誤!"}
    // {"state" : false, "message" : "未傳遞任何參數!"}

    //file_get_contents函數為用於讀取文件內容，整行為讀取POST請求的數據並儲存在變數$data裡
    $data = file_get_contents("php://input", "r");
    if($data != ""){
        $mydata = array();
        //json_decode()函數為用於將JSON格式的字符串轉換為PHP中的數組或對象
        //整句為使用json_decode()函數將JSON格式的數據解碼為PHP關聯數組，並儲存在$mydata裡
        $mydata = json_decode($data, true);
        if(isset($mydata["Username"]) && $mydata["Username"] != ""){
            $p_Username = $mydata["Username"];

            $servername = "localhost";
            $username = "owner01";
            $password = "123456";
            $dbname = "testdb";

            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if(!$conn){
                die("連線失敗".mysqli_connect_error());
            }

            //查詢 member 表中是否存在與用戶提交的username相同的記錄
            $sql = "SELECT Username FROM member WHERE Username = '$p_Username'";
            //執行 SQL 查詢，將結果存儲在變數 $result 中
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) == 0){
                //帳號不存在, 可以使用
                echo '{"state" : true, "message" : "帳號不存在, 可以使用!"}';
            }else{
                //帳號存在, 不可以使用
                echo '{"state" : false, "message" : "帳號已存在,不可以使用!"}';
            }
            mysqli_close($conn);
        }else{
            echo '{"state" : false, "message" : "傳遞參數格式錯誤!"}';
        }
    }else{
        echo '{"state" : false, "message" : "未傳遞任何參數!"}';
    }
?>