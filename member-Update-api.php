<?php
    //input: {"ID":"XX", "Email":"XXXXX"}
    // {"state" : true, "message" : "更新成功!"}
    // {"state" : false, "message" : "更新失敗!"}
    // {"state" : false, "message" : "傳遞參數格式錯誤!"}
    // {"state" : false, "message" : "未傳遞任何參數!"}

    // file_get_contents()為PHP 函數，用於讀取檔案內容。
    // php://input" 從客戶端發送到伺服器的原始請求資料。
    // r 唯讀模式，只能被讀取，不能被寫入或修改。
    $data = file_get_contents("php://input", "r");
    if($data != ""){
        $mydata = array();
        $mydata = json_decode($data, true);
        //isset是PHP函數，isset($mydata["ID"])檢查$mydata是否存在鍵"ID"
        //檢查ID，Email是否存在，且不為空
        if(isset($mydata["ID"]) && isset($mydata["Email"]) && $mydata["ID"] != "" && $mydata["Email"] != ""){
            $p_ID = $mydata["ID"];//從$mydata 中取"ID"值，賦值給$p_ID
            $p_Email = $mydata["Email"];

            $servername = "localhost";
            $username = "owner01";
            $password = "123456";
            $dbname = "testdb";

            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if(!$conn){
                die("連線失敗".mysqli_connect_error());
            }

            // SQL 查詢語句，用來更新資料庫中的資料。查詢目的是將 member 資料表中滿足條件的記錄的 Email 欄位值更新為 $p_Email，條件是 ID 欄位的值等於 $p_ID。
            $sql = "UPDATE member SET Email = '$p_Email' WHERE ID = '$p_ID'";
            if(mysqli_query($conn, $sql)){
                //更新成功
                echo '{"state" : true, "message" : "更新成功!"}';
            }else{
                //更新失敗
                echo '{"state" : false, "message" : "更新失敗!'.$sql.'"}';
            }
            mysqli_close($conn);
        }else{
            echo '{"state" : false, "message" : "傳遞參數格式錯誤!"}';
        }
    }else{
        echo '{"state" : false, "message" : "未傳遞任何參數!"}';
    }
?>
