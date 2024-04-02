<?php
    // {"state" : true, "data": "所有會員資料", "message" : "讀取成功!"}
    // {"state" : false, "message" : "讀取失敗!"}

    $servername = "localhost";
    $username = "owner01";
    $password = "123456";
    $dbname = "testdb";

    // 建立資料庫連線，[mysqli_connect()是PHP函數，建立到 MySQL 數據庫的連接]
    // $conn 存儲與資料庫建立的連接的變數(主機名,使用者名,密碼,資料庫名)。
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if(!$conn){ // "!"表示否定，!$conn為資料庫連線未成功
        die("連線失敗".mysqli_connect_error()); //固定MySQL函數名稱，資料庫連接錯誤的描述
    }

    // 準備 SQL 查詢語句從資料表 member 中選擇所有欄位，並按照 ID 欄位以降序（DESC）排列。然後執行這個查詢。
    //  SELECT *選擇所有欄位，ORDER BY對資料進行排序，DESC降序
    $sql = "SELECT * FROM member ORDER BY ID DESC";
    $result = mysqli_query($conn, $sql); //執行 SQL 查詢並將結果儲存在 $result 變數中[mysqli_query()是MySQL 查詢的函數，$sql上面定義的查詢語句]

    // 執行 SQL 查詢，檢查是否有查詢到資料，這一行檢查是否有資料返回，如果查詢到的資料筆數大於 0，則表示有資料被查詢到。
    // mysqli_num_rows()用於計算結果集中的資料行數量(筆數量)，存於$result變數中存儲了整個結果集，包含了所有查詢到的資料行。
    if(mysqli_num_rows($result) > 0){
        $mydata = array(); // 將查詢到的每一筆資料轉換為關聯陣列並儲存到 $mydata 陣列中
        // mysqli_fetch_assoc($result)是一個函數呼叫，從資料庫結果集中取得下一行資料並以關聯式陣列的形式返回。每次呼叫都會返回結果集中的下一行資料，，直到所有資料都被取得。
        // while迴圈條件是從結果集中取得下一行資料並將其存入 $row 變數中，還有資料可取得時，迴圈會執行。
        while($row = mysqli_fetch_assoc($result)){
            $mydata[] = $row; // 每次迭代時，將 $row 陣列加入到 $mydata 陣列的末尾，從而形成一個二維陣列，
        }
        // 將 $mydata 陣列中的資料以 JSON 格式輸出，包括成功的狀態和訊息(input)
        // json_encode($mydata)將 PHP 的陣列轉換為 JSON 格式的字串。
        echo '{"state" : true, "data": '.json_encode($mydata).', "message" : "讀取成功!"}'; //state" : true為操作狀態為成功，"data": '.json_encode($mydata)資料 轉換 JSON 格式
    }else{
        // 如果沒有查詢到資料，輸出 JSON 格式的失敗訊息
        echo '{"state" : false, "message" : "讀取失敗!"}';
    }

    // 最後關閉資料庫連線，確保資源被正確釋放
    mysqli_close($conn);// mysqli_close關閉與 MySQL 數據庫連接的函數，$conn 是與數據庫建立的連接對象。
?>

<!-- 二維陣列
$students = [
    ["John", 20, 85],
    ["Jane", 22, 92],
    ["Bob", 21, 78]
]; -->
