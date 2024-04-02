<?php
    //input: {"Username":"XX", "Password":"XXX", "Email":"XXXXX"}
    // {"state" : true, "message" : "註冊成功!"}
    // {"state" : false, "message" : "註冊失敗!"}
    // {"state" : false, "message" : "傳遞參數格式錯誤!"}
    // {"state" : false, "message" : "未傳遞任何參數!"}

    //新增一筆固定資料
    //請求數據儲存在$data裡
    $data = file_get_contents("php://input", "r");
    //如果$data不為空則成功獲取數據進入下個條件判斷
    if($data != ""){
        //創建一個空陣列 $mydata
        $mydata = array();
        //使用 json_decode() 函數將 JSON 格式的數據解碼為 PHP 關聯數組，存儲在 $mydata 中
        $mydata = json_decode($data, true);
        //檢查$mydata 中是否包含 "Username"、"Password" 和 "Email" 三個鍵的值，並且這些值是否不是空值
        if(isset($mydata["Username"]) && isset($mydata["Password"]) && isset($mydata["Email"]) && $mydata["Username"] != "" && $mydata["Password"] != "" && $mydata["Email"] != ""){
            //20.23.24行如果欄位都存在且不為空則會得到username.Email還有對密碼加密
            $p_Username = $mydata["Username"];
            //密碼加密PASSWORD_DEFAULT
            //把編碼後的密碼存到$p_Password
            $p_Password = password_hash($mydata["Password"], PASSWORD_DEFAULT);
            $p_Email = $mydata["Email"];
            $p_Level = "B100";// 新增會員等級為 B100

            $servername = "localhost";
            $username = "owner01";
            $password = "123456";
            $dbname = "testdb";

            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if(!$conn){
                die("連線失敗".mysqli_connect_error());
            }
            //欄位 UID01 空值
            //將用戶提交的註冊資訊插入到數據庫的 member 表中
            $sql = "INSERT INTO member(Username, Password, Email, UID01, Level) VALUES('$p_Username', '$p_Password', '$p_Email', '', '$p_Level')";
            if(mysqli_query($conn, $sql)){
                //新增成功
                echo '{"state" : true, "message" : "註冊成功!"}';
            }else{
                //新增失敗
                echo '{"state" : false, "message" : "註冊失敗!"}';
            }
            mysqli_close($conn);
        }else{
            echo '{"state" : false, "message" : "傳遞參數格式錯誤!"}';
        }
    }else{
        echo '{"state" : false, "message" : "未傳遞任何參數!"}';
    }
?>