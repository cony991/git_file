<?php
    //input: {"ID":"1"}
    // output:
    // {"state" : true, "message" : "刪除成功!"}
    // {"state" : false, "message" : "刪除失敗!"}
    // {"state" : false, "message" : "傳遞參數格式錯誤!"}
    // {"state" : false, "message" : "未傳遞任何參數!"}
    $data = file_get_contents("php://input", "r");
    if($data != ""){
        $mydata = array();
        $mydata = json_decode($data, true);
        if(isset($mydata["ID"]) && $mydata["ID"] != ""){
            $p_ID = $mydata["ID"];

            $servername = "localhost";
            $username = "owner01";
            $password = "123456";
            $dbname = "testdb";

            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if(!$conn){
                die("連線失敗".mysqli_connect_error());
            }

            //WHERE ID = '$p_ID 指定刪除符合特定 ID 的記錄。
            //從資料表 member 中刪除符合特定 ID 的記錄。
            $sql = "DELETE FROM member WHERE ID = '$p_ID'";
            //mysqli_query為MySQL 查詢的函數
            if(mysqli_query($conn, $sql)){
                //刪除成功
                echo '{"state" : true, "message" : "刪除成功!"}';
            }else{
                //刪除失敗
                echo '{"state" : false, "message" : "刪除失敗!"}';
            }
            mysqli_close($conn);
        }else{
            echo '{"state" : false, "message" : "傳遞參數格式錯誤!"}';
        }
    }else{
        echo '{"state" : false, "message" : "未傳遞任何參數!"}';
    }
?>